# ProTest_moriya

## Pro入会テスト

**タイトル** : 模擬案件フリマアプリの追加機能実装

---

## 📋 目次

- [環境構築手順](#環境構築手順)
- [アカウント情報](#アカウント情報)
- [使用技術](#使用技術)
- [URL一覧](#url一覧)
- [Stripe設定](#stripe設定)
- [主な機能](#主な機能)
- [トラブルシューティング](#トラブルシューティング)
- [開発メモ](#開発メモ)
- [ライセンス](#ライセンス)
- [開発者](#開発者)
- [ER図](#er図)
- [PHPUnitテスト](#phpunitテスト)

---

## 環境構築手順

### 1. リポジトリのクローン

```bash
git clone git@github.com:km20250118/ProTest_moriya.git
```

Dockerデスクトップアプリを立ち上げる

```bash
docker-compose up -d --build
```

### 2. Laravel環境構築

```bash
# 1. PHPコンテナに入る
docker-compose exec php bash

# 2. Composerパッケージのインストール
composer install

# 3. 環境変数ファイルのコピー
cp .env.example .env

# 4. .envファイルに以下の環境変数を追加
# DB_CONNECTION=mysql
# DB_HOST=mysql
# DB_PORT=3306
# DB_DATABASE=laravel_db
# DB_USERNAME=laravel_user
# DB_PASSWORD=laravel_pass

# 5. アプリケーションキーの生成
php artisan key:generate

# 6. マイグレーションの実行
php artisan migrate

# 7. シーディングの実行
php artisan db:seed

# 8. シンボリックリンクの作成
php artisan storage:link
```

---

## アカウント情報

### テストアカウント

| 項目 | 内容 |
|------|------|
| **一般ユーザー1** | <general1@gmail.com> / password |
| **一般ユーザー2** | <general2@gmail.com> / password |
| **テストユーザー** | <test@example.com> / 12345678 |

**注**: 全てのシードユーザーのパスワードは `password` です

---

## 使用技術

| 技術 | バージョン |
|------|-----------|
| **Laravel** | 8.83.29 |
| **Nginx** | 1.21.1 |
| **PHP** | 8.1-fpm |
| **MySQL** | 8.0 |
| **phpMyAdmin** | latest |
| **MailHog** | latest |

---

## URL一覧

### アプリケーション

| サービス | URL |
|---------|-----|
| **開発環境** | [http://localhost](http://localhost) |
| **phpMyAdmin** | [http://localhost:8080](http://localhost:8080) |
| **MailHog** | [http://localhost:8025](http://localhost:8025) |

### 外部サービス

| サービス | URL |
|---------|-----|
| **Stripe Dashboard** | [https://dashboard.stripe.com/acct_1SN3miFbW3HpXmAy/test/dashboard](https://dashboard.stripe.com/acct_1SN3miFbW3HpXmAy/test/dashboard) |

### 連絡先

| 項目 | 内容 |
|------|------|
| **メールアドレス** | [kiyotaka.moriya@gmail.com](mailto:kiyotaka.moriya@gmail.com) |
| **パスワード** | coachtechtest |

---

## Stripe設定

### APIキーの設定

`.env`ファイルに以下を追加してください：

```env
STRIPE_PUBLIC_KEY=pk_test_あなたの公開キー
STRIPE_SECRET_KEY=sk_test_あなたのシークレットキー
```

**⚠️ セキュリティ上の理由により、実際のAPIキーはこのREADMEには記載していません。**

### Stripe APIキーの取得方法

1. [Stripe Dashboard](https://dashboard.stripe.com/test/apikeys)にアクセス
2. 「開発者」→「APIキー」を選択
3. **テストモード**であることを確認
4. 以下をコピー：
   - 公開可能キー（`pk_test_`で始まる）
   - シークレットキー（`sk_test_`で始まる）

### テスト決済用カード情報

| 項目 | 内容 |
|------|------|
| **カード番号** | 4242 4242 4242 4242 |
| **有効期限** | 12/34 |
| **CVC** | 123 |
| **郵便番号** | 12345 |

---

## 主な機能

### ユーザー機能

- ✅ 会員登録
- ✅ ログイン/ログアウト
- ✅ メール認証
- ✅ プロフィール編集
- ✅ ユーザー評価表示（星評価）

### 商品機能

- ✅ 商品一覧表示
- ✅ 商品詳細表示
- ✅ 商品検索
- ✅ 商品出品
- ✅ いいね機能
- ✅ コメント機能

### 決済機能

- ✅ Stripe決済統合
- ✅ クレジットカード決済
- ✅ コンビニ払い
- ✅ 購入履歴管理
- ✅ 決済履歴保存
- ✅ 住所未設定時の自動リダイレクト

### チャット機能（模擬案件01）

- ✅ 取引中商品一覧の表示
- ✅ 購入者・出品者間のメッセージ送受信
- ✅ テキスト＋画像メッセージ送信
- ✅ 画像プレビュー機能（送信前確認）
- ✅ メッセージ編集・削除（ソフトデリート）
- ✅ 未読メッセージ管理
- ✅ 未読バッジ表示（赤文字＋数字）
- ✅ localStorage による下書き保存
- ✅ サイドバーでの取引商品切り替え
- ✅ レスポンシブデザイン（タブレット・PC対応）

### 評価機能（模擬案件01）

- ✅ 購入者の取引完了ボタン＋評価モーダル
- ✅ 出品者の自動評価モーダル表示
- ✅ 5段階星評価
- ✅ 取引ステータス管理（in_transaction → buyer_completed → completed）
- ✅ 取引完了メール通知（MailHog経由）
- ✅ マイページでのユーザー評価平均表示

---

## MailHog（メール送信テスト）

### アクセス方法

開発環境では、メール送信のテストに **MailHog** を使用しています。

- URL: [http://localhost:8025](http://localhost:8025)
- 送信された全てのメールがMailHogで確認できます

### 確認できるメール

- ✅ メール認証メール
- ✅ 取引完了通知メール（購入者評価後に出品者へ送信）

---

## トラブルシューティング

### データベース接続エラー

```bash
docker-compose restart mysql
docker-compose exec php php artisan config:clear
```

### Stripe決済エラー

```bash
docker-compose exec php bash
php artisan config:clear
php artisan cache:clear
```

### 画像が表示されない場合

```bash
docker-compose exec php php artisan storage:link
```

### メール送信の確認

MailHog（[http://localhost:8025](http://localhost:8025)）でメールを確認できます。

### Nginxアップロードサイズエラー

画像アップロード時に413エラーが出る場合、`docker/nginx/default.conf` で `client_max_body_size` が設定されているか確認してください。

```bash
docker-compose restart nginx
```

---

## 開発メモ

### キャッシュクリア

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### マイグレーションのリセット

```bash
php artisan migrate:fresh --seed
```

### テスト実行

```bash
# 全テスト実行
php artisan test

# 詳細表示
php artisan test --testdox

# 特定のテストのみ実行
php artisan test --filter ChatControllerTest
php artisan test --filter RatingControllerTest
```

---

## ライセンス

このプロジェクトは学習目的で作成されました。

---

## 開発者

[km20250118](https://github.com/km20250118)

---

## ER図

 ![](2025-10-28-18-12-32.png)

---

## PHPUnitテスト

### テスト実行方法

**すべてのテストを実行**:

```bash
docker-compose exec php php artisan test
```

**テスト実行結果詳細表示**:

```bash
docker-compose exec php php artisan test --testdox
```

**特定のテストのみ実行**:

```bash
docker-compose exec php php artisan test --filter ChatControllerTest
docker-compose exec php php artisan test --filter RatingControllerTest
```

### 最新テスト実行結果

**実行日**: 2025年2月2日  
**テスト数**: 63  
**成功**: ✅ 63（全て合格）  
**失敗**: ❌ 0  
**成功率**: 100%  
**実行時間**: 1.88秒  

**テスト内訳**:

- Unit Tests: 1
- Feature Tests: 62
  - 認証機能: 15テスト
  - 商品機能: 36テスト
  - 住所機能: 4テスト
  - ユーザー機能: 6テスト
  - 支払い機能: 2テスト
  - **チャット機能: 4テスト** ← 新規追加
  - **評価機能: 3テスト** ← 新規追加
  - その他: 2テスト

```
   PASS  Tests\Unit\ExampleTest
  ✓ example

   PASS  Tests\Feature\Address\AddressChangeTest
  ✓ address edit page displays
  ✓ user can update address via profile
  ✓ purchase address change page displays
  ✓ user can update address during purchase

   PASS  Tests\Feature\Auth\EmailVerificationTest
  ✓ user can register
  ✓ email verification notice page displays
  ✓ verification email is sent
  ✓ user can verify email

   PASS  Tests\Feature\Auth\LoginTest
  ✓ login validation email required
  ✓ login validation password required
  ✓ login fails with invalid credentials
  ✓ login success with valid credentials

   PASS  Tests\Feature\Auth\LogoutTest
  ✓ user can logout

   PASS  Tests\Feature\Auth\RegisterTest
  ✓ register validation name required
  ✓ register validation email required
  ✓ register validation password required
  ✓ register validation password must be string
  ✓ register validation password confirmation mismatch
  ✓ register success with valid data

   PASS  Tests\Feature\ChatControllerTest
  ✓ チャット画面が正常に表示される
  ✓ メッセージを送信できる
  ✓ 自分のメッセージを編集できる
  ✓ 自分のメッセージを削除できる

   PASS  Tests\Feature\ExampleTest
  ✓ example

   PASS  Tests\Feature\Item\CommentTest
  ✓ authenticated user can post comment
  ✓ guest cannot post comment
  ✓ comment validation content required
  ✓ comment validation max length

   PASS  Tests\Feature\Item\ItemCreateTest
  ✓ sell page displays
  ✓ user can create item with all information
  ✓ item creation validates required fields
  ✓ guest cannot access sell page

   PASS  Tests\Feature\Item\ItemDetailTest
  ✓ item detail page displays
  ✓ item detail displays all information
  ✓ item categories are displayed correctly

   PASS  Tests\Feature\Item\ItemIndexTest
  ✓ item index page displays
  ✓ all items are displayed
  ✓ sold items display correctly

   PASS  Tests\Feature\Item\ItemSearchTest
  ✓ search by item name
  ✓ search shows matching results

   PASS  Tests\Feature\Item\LikeTest
  ✓ user can like item
  ✓ user can unlike item
  ✓ guest cannot like item
  ✓ like count increases and decreases

   PASS  Tests\Feature\Item\MyListTest
  ✓ liked items are displayed
  ✓ sold liked items display correctly
  ✓ mylist requires authentication

   PASS  Tests\Feature\Item\PurchaseTest
  ✓ purchase page displays
  ✓ user can purchase item
  ✓ purchase success page displays
  ✓ address change page displays

   PASS  Tests\Feature\Payment\PaymentMethodTest
  ✓ payment method selection page loads
  ✓ payment form displays

   PASS  Tests\Feature\RatingControllerTest
  ✓ 購入者が評価を投稿できる
  ✓ 出品者が評価を投稿できる
  ✓ ユーザーの平均評価が正しく計算される

   PASS  Tests\Feature\User\UserProfileTest
  ✓ mypage displays
  ✓ user profile displays all information
  ✓ guest cannot access mypage

   PASS  Tests\Feature\User\UserProfileUpdateTest
  ✓ profile edit page displays
  ✓ profile shows current user information
  ✓ user can update profile

  Tests:  63 passed
  Time:   1.88s
```

### テストカバレッジ

#### チャット機能テスト

- ✅ チャット画面の表示権限チェック
- ✅ メッセージ送信機能
- ✅ メッセージ編集機能（権限チェック含む）
- ✅ メッセージ削除機能（ソフトデリート）

#### 評価機能テスト

- ✅ 購入者評価投稿とメール送信
- ✅ 出品者評価投稿と取引ステータス更新
- ✅ ユーザー平均評価の計算ロジック

---

## 追加実装機能（模擬案件01）

### 実装ファイル一覧

#### マイグレーション

- `2025_10_08_123000_create_chat_messages_table.php`
- `2025_10_08_123100_create_ratings_table.php`
- `2025_10_08_122800_add_transaction_status_to_items_table.php`

#### モデル

- `app/Models/ChatMessage.php`
- `app/Models/Rating.php`

#### コントローラー

- `app/Http/Controllers/ChatController.php`
- `app/Http/Controllers/RatingController.php`

#### リクエスト

- `app/Http/Requests/ChatMessageRequest.php`
- `app/Http/Requests/RatingRequest.php`

#### メール

- `app/Mail/TransactionCompleteMail.php`

#### ビュー

- `resources/views/chat/show.blade.php`
- `resources/views/chat/edit.blade.php`
- `resources/views/mypage/_transaction_items.blade.php`
- `resources/views/mail/transaction_complete.blade.php`

#### テスト

- `tests/Feature/ChatControllerTest.php`
- `tests/Feature/RatingControllerTest.php`

---

## 実装した仕様（模擬案件01）

### チャット機能仕様

| 機能ID | 機能名 | 説明 |
|--------|--------|------|
| FN001 | 取引中商品一覧 | マイページに「取引中の商品」タブを追加 |
| FN002 | チャット画面遷移 | 商品カードクリックでチャット画面へ遷移 |
| FN003 | サイドバー切り替え | 左サイドバーで他の取引商品に切り替え可能 |
| FN004 | メッセージソート | 新規メッセージ順にソート |
| FN005 | 未読数表示 | 未読メッセージ数を赤バッジで表示 |
| FN006 | メッセージ送信 | テキスト＋画像メッセージ送信 |
| FN007 | 既読管理 | チャット画面表示時に自動既読 |
| FN008 | メッセージ一覧 | 名前バー＋グレーメッセージ枠で表示 |
| FN009 | 下書き保存 | localStorageで入力中テキスト保持 |
| FN010 | メッセージ編集 | 自分のメッセージのみ編集可能 |
| FN011 | メッセージ削除 | 自分のメッセージのみ削除可能（ソフトデリート） |

### 評価機能仕様

| 機能ID | 機能名 | 説明 |
|--------|--------|------|
| FN012 | 購入者評価 | 「取引を完了する」ボタンで評価モーダル表示 |
| FN013 | 出品者評価 | 購入者完了後、チャット画面で自動モーダル表示 |
| FN014 | 評価後遷移 | 評価送信後は商品一覧画面へリダイレクト |
| FN015 | ステータス管理 | in_transaction → buyer_completed → completed |
| FN016 | メール通知 | 購入者評価後、出品者へメール送信（MailHog） |

---

## 技術的な工夫

### セキュリティ

- ✅ CSRFトークンによる保護
- ✅ アクセス権限チェック（購入者・出品者のみアクセス可能）
- ✅ バリデーションによる入力チェック
- ✅ 重複評価の防止

### パフォーマンス

- ✅ EagerロードによるN+1問題の回避
- ✅ インデックスによるクエリ最適化
- ✅ ソフトデリートによるデータ保持

### UX/UI

- ✅ レスポンシブデザイン（タブレット768-850px、PC1400-1540px対応）
- ✅ 画像プレビュー機能
- ✅ ホバーエフェクト
- ✅ リアルタイム既読管理
- ✅ localStorage による下書き保存

---

# ProTest_moriya
