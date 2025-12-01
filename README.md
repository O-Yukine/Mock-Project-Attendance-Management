# 模擬案件２　勤怠管理アプリ

## 環境構築

### Docker ビルド

1. リポジトリをクローン

git clone git@github.com:O-Yukine/Mock-Project-Attendance-Management.git

2. Docker アプリを立ち上げる

3. Docker ビルド

docker-compose up -d --builed

### Laravel 環境構築

1. PHP コンテナに入る

docker-compose exec php bash

2. Composer パッケージをインストール

composer install

3. 環境設定ファイルをコピー

cp .env.example .env

4. .env ファイルを編集

DB_HOST=mysql
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

5. アプリケーションキーの作成

php artisan key:generate

6. マイグレーションの実行

php artisan migrate

7. シーディングの実行

php artisan db:seed

### ユーザー登録時のメール認証システムの設定(mailtrap の利用)

1. mailtrap のアカウントを作成

2. mailtrap より SMTP を取得して、.env ファイルの修正

MAIL_MAILER=
MAIL_HOST=
MAIL_PORT= MAIL_USERNAME=
MAIL_PASSWORD= MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=任意のメールアドレス
MAIL_FROM_NAME="${APP_NAME}"

3. ユーザー登録時に mailtrap にメールが送られてくるので、そのメールよりメール認証を完了する

### ユニットテストとテスト環境の構築

#### テスト用のデータベースを作る

1. MySQL のコンテナへ入る

docker-compose exex mysql bash  
mysql -u root -p

2. laravel_test テーブルを作成

CREATE DATABASE laravel_test;

3. テスト用.env を作る

cp .env.testing

4. .env.testing ファイルを編集

APP_ENV=test
APP_KEY=

DB_DATABASE=laravel_test
DB_USERNAME=root
DB_PASSWORD=root

5. テスト用アプリケーションキーの作成

6. マイグレーションの実行

php artisan migrate --env=testing

7. テストの実行は以下のコマンド

php artisan test tests/Feature

もしくは

vender/bin/phpunit tests/Feature

### 使用技術

- PHP8.1
- Laravel8.83.8
- MySQL8.0.26

### ER 図

### URL

- 開発環境:http://localhost/
- phpMyAdmin:http://localhost:8080/
