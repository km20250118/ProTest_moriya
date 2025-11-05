# mogitest_01 完全版テストスイート (52テスト)

## 📦 内容

### テストファイル (16ファイル / 52テスト)
- Auth/ (4ファイル / 15テスト)
- Item/ (7ファイル / 25テスト)  
- Payment/ (1ファイル / 2テスト)
- Address/ (1ファイル / 4テスト)
- User/ (2ファイル / 6テスト)

### ファクトリファイル (3ファイル)
- CategoryFactory.php
- CommentFactory.php
- ProfileFactory.php

## 🚀 インストール

```bash
# 1. ZIPを解凍
cd ~/Downloads
unzip mogitest_01_complete_52tests.zip

# 2. プロジェクトに配置
cd ~/Projects/mogitest_01
cp -r ~/Downloads/Feature/* src/tests/Feature/
cp ~/Downloads/factories/* src/database/factories/

# 3. テスト実行
cd src
docker-compose exec php php artisan test
```

## ✅ 期待される結果

```
Tests: 52 passed
```
