# ProTest_moriya

## Pro入会テスト

**タイトル**:模擬案件フリマアプリの追加機能実装

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
- [PHPUnitテスト](#PHPUnitテスト)

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
| **ID** | [test@example.com](mailto:test@example.com) |
| **パスワード** | 12345678 |

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
- ✅ 購入履歴管理
- ✅ 決済履歴保存

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

### メール送信の確認

MailHog（[http://localhost:8025](http://localhost:8025)）でメールを確認できます。

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

**すべてのテストを実行**: docker-compose exec php php artisan test  
**テスト実行結果詳細表示**: docker-compose exec php php artisan test --verbose  

### 最新テスト実行結果

**実行日**: 2025年11月5日  
**テスト数**: 56  
**成功**: ✅ 56（全て合格）  
**失敗**: ❌ 0  
**成功率**: 100%  
**実行時間**: 1.92秒  
**テスト内訳**:
- Unit Tests: 1
- Feature Tests: 55
  - 認証機能: 14テスト
  - 商品機能: 36テスト
  - 住所機能: 4テスト
  - ユーザー機能: 6テスト
  - 支払い機能: 2テスト
  - その他: 2テスト
  
````
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

   PASS  Tests\Feature\User\UserProfileTest
  ✓ mypage displays
  ✓ user profile displays all information
  ✓ guest cannot access mypage

   PASS  Tests\Feature\User\UserProfileUpdateTest
  ✓ profile edit page displays
  ✓ profile shows current user information
  ✓ user can update profile
# ProTest_moriya
