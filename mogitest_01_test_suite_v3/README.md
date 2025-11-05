# mogitest_01 PHPUnit テストスイート v3.0

## インストール手順

### 1. ZIPを解凍
```bash
cd ~/Downloads
unzip mogitest_01_test_suite_v3.zip
```

### 2. プロジェクトに配置
```bash
cd /path/to/mogitest_01
cp -r ~/Downloads/Feature/* tests/Feature/
```

### 3. テストDB作成
```bash
docker-compose exec mysql mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS laravel_db_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON laravel_db_test.* TO 'laravel_user'@'%'; FLUSH PRIVILEGES;"
```

### 4. 環境設定
```bash
docker-compose exec php bash
cp .env .env.testing
# .env.testingのDB_DATABASEをlaravel_db_testに変更
```

### 5. マイグレーション
```bash
php artisan migrate --env=testing --force
```

### 6. テスト実行
```bash
php artisan test
```

## 含まれるテスト

- Auth/RegisterTest.php (6テスト)
- Auth/LoginTest.php (4テスト)
- Auth/LogoutTest.php (1テスト)
- Item/ItemIndexTest.php (2テスト)

合計: 13テストケース
