# Docker・Git基礎学習サンプル

PHP学習者向けのDocker環境構築とGit操作を学習するためのサンプルプロジェクトです。

## 📋 概要

このサンプルでは、以下の技術を学習できます：

- **Docker**: コンテナ技術・Docker Compose・マルチコンテナ構成
- **インフラ構築**: Nginx・PHP-FPM・MySQL・Redisの統合環境
- **開発環境**: ローカル開発に最適化された設定
- **Git**: バージョン管理・チーム開発ワークフロー
- **DevOps**: Makefile・自動化スクリプト・CI/CD基礎

## 🏗️ アーキテクチャ

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Nginx     │────│   PHP-FPM   │────│   MySQL     │
│ (Web Server)│    │ (App Server)│    │ (Database)  │
│   :80       │    │    :9000    │    │   :3306     │
└─────────────┘    └─────────────┘    └─────────────┘
                            │
                   ┌─────────────┐    ┌─────────────┐
                   │   Redis     │    │  phpMyAdmin │
                   │  (Cache)    │    │ (DB管理)    │
                   │   :6379     │    │   :80       │
                   └─────────────┘    └─────────────┘
```

## 🚀 クイックスタート

### 1. 前提条件
```bash
# Docker & Docker Composeの確認
docker --version
docker-compose --version

# Gitの確認
git --version
```

### 2. プロジェクトのクローン
```bash
git clone <repository-url>
cd next04_docker_git/sample
```

### 3. 環境設定
```bash
# 環境設定ファイルをコピー
cp .env.example .env

# 必要に応じて .env を編集
vim .env
```

### 4. Docker環境の起動
```bash
# 初回セットアップ（推奨）
make setup

# または手動でビルド・起動
make build
make up
```

### 5. アクセス確認
- **Webアプリケーション**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **MailHog**: http://localhost:8025

## 🐳 Docker構成詳細

### サービス一覧

| サービス | 役割 | ポート | 説明 |
|---------|------|--------|------|
| **web** | Webサーバー | 8080:80 | Nginx (リバースプロキシ) |
| **app** | アプリサーバー | - | PHP 8.2 FPM |
| **database** | データベース | 3306:3306 | MySQL 8.0 |
| **redis** | キャッシュ | 6379:6379 | Redis (セッション・キャッシュ) |
| **phpmyadmin** | DB管理 | 8081:80 | データベース管理UI |
| **mailhog** | メールテスト | 8025:8025 | SMTP テスト環境 |
| **node** | フロントエンド | 3000:3000 | Node.js 18 |
| **composer** | 依存関係管理 | - | PHP パッケージマネージャ |

### ボリューム構成
```yaml
volumes:
  mysql_data: # MySQLデータの永続化
    driver: local
  redis_data: # Redisデータの永続化
    driver: local
```

### ネットワーク
```yaml
networks:
  php_learning_network:
    driver: bridge
    subnet: 172.20.0.0/16
```

## 🛠️ Makefile操作

便利なMakefileコマンドが用意されています：

### 基本操作
```bash
make help          # ヘルプ表示
make setup         # 初回環境構築
make up            # コンテナ起動
make down          # コンテナ停止・削除
make restart       # コンテナ再起動
make status        # 状態確認
```

### 開発作業
```bash
make logs          # 全ログ表示
make logs-app      # PHPアプリログ
make shell-app     # PHPコンテナ接続
make mysql         # MySQLクライアント接続
make redis-cli     # Redis CLI接続
```

### 依存関係管理
```bash
make composer-install    # Composer install
make composer-update     # Composer update
make npm-install        # npm install
make npm-dev           # npm 開発サーバー
```

### データベース
```bash
make db-seed           # サンプルデータ投入
make db-backup         # データベースバックアップ
make db-restore backup_file=path/to/file.sql  # データベース復元
```

### メンテナンス
```bash
make clean             # クリーンアップ
make clean-all         # 完全削除
make security-scan     # セキュリティスキャン
```

## 📁 ディレクトリ構成

```
sample/
├── docker/                    # Docker設定
│   ├── nginx/
│   │   └── default.conf      # Nginx設定
│   ├── php/
│   │   ├── Dockerfile        # PHPコンテナ設定
│   │   └── php.ini           # PHP設定
│   ├── mysql/
│   │   ├── my.cnf            # MySQL設定
│   │   └── init/             # 初期化SQL
│   └── redis/
│       └── redis.conf        # Redis設定
├── src/                      # アプリケーション
│   ├── public/              # Web公開ディレクトリ
│   │   └── index.php        # エントリーポイント
│   └── index.php            # メインアプリケーション
├── docker-compose.yml        # Docker Compose設定
├── Makefile                 # 操作コマンド
├── .env.example             # 環境設定例
├── .gitignore              # Git除外設定
└── README.md               # このファイル
```

## 🔧 カスタマイズ

### 1. PHPの設定変更
`docker/php/php.ini` を編集：
```ini
memory_limit = 512M
upload_max_filesize = 32M
post_max_size = 64M
```

### 2. Nginxの設定変更
`docker/nginx/default.conf` を編集：
```nginx
# キャッシュ設定
location ~* \.(js|css|png|jpg)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

### 3. MySQLの設定変更
`docker/mysql/my.cnf` を編集：
```ini
[mysqld]
innodb_buffer_pool_size = 256M
max_connections = 200
```

### 4. 新しいサービス追加
`docker-compose.yml` に追加：
```yaml
services:
  elasticsearch:
    image: elasticsearch:7.17
    ports:
      - "9200:9200"
    environment:
      - discovery.type=single-node
```

## 🔍 トラブルシューティング

### よくある問題と解決法

#### 1. ポート競合
```bash
# エラー: port is already allocated
# 解決: .env でポート番号変更
WEB_PORT=8081
MYSQL_PORT=3307
```

#### 2. 権限エラー
```bash
# Linux/macOSでの権限問題
sudo chown -R $USER:$USER ./src
chmod -R 755 ./src
```

#### 3. Docker Compose バージョン
```bash
# 古いDocker Composeでエラーが出る場合
# version: '3.8' → version: '3.7' に変更
```

#### 4. データベース接続エラー
```bash
# コンテナの起動順序を確認
make status
make logs-db

# データベース初期化が必要な場合
make down
docker volume rm php_learning_mysql_data
make up
```

## 📚 学習ポイント

### Docker基礎
- **Dockerfile**: カスタムイメージの作成
- **docker-compose.yml**: マルチコンテナ構成
- **ボリューム**: データ永続化
- **ネットワーク**: コンテナ間通信
- **環境変数**: 設定の外部化

### 開発環境のベストプラクティス
- **設定の分離**: 本番・開発・テスト環境の使い分け
- **セキュリティ**: 最小権限・シークレット管理
- **監視・ログ**: アプリケーション状態の可視化
- **自動化**: Makefile・スクリプトによる効率化

### Git操作
```bash
# 基本的なGitワークフロー
git init
git add .
git commit -m "Initial commit"
git remote add origin <repository-url>
git push -u origin main

# ブランチ操作
git checkout -b feature/new-feature
git add .
git commit -m "Add new feature"
git push origin feature/new-feature

# マージ
git checkout main
git merge feature/new-feature
git push origin main
```

## 🚀 発展学習

### 1. CI/CDパイプライン
```yaml
# .github/workflows/ci.yml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Build Docker images
        run: make build
      - name: Run tests
        run: make test
```

### 2. 本番環境対応
```yaml
# docker-compose.prod.yml
version: '3.8'
services:
  web:
    restart: always
    environment:
      - APP_ENV=production
  app:
    environment:
      - APP_DEBUG=false
```

### 3. Kubernetesへの移行
```yaml
# k8s/deployment.yml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: php-app
spec:
  replicas: 3
  selector:
    matchLabels:
      app: php-app
  template:
    spec:
      containers:
      - name: app
        image: php-learning:latest
        ports:
        - containerPort: 9000
```

### 4. 監視システム
```yaml
# monitoring/docker-compose.yml
services:
  prometheus:
    image: prom/prometheus
    ports:
      - "9090:9090"
  grafana:
    image: grafana/grafana
    ports:
      - "3001:3000"
```

## 🔗 参考資料

- [Docker公式ドキュメント](https://docs.docker.com/)
- [Docker Compose リファレンス](https://docs.docker.com/compose/)
- [PHP Docker イメージ](https://hub.docker.com/_/php)
- [MySQL Docker イメージ](https://hub.docker.com/_/mysql)
- [Nginx Docker イメージ](https://hub.docker.com/_/nginx)
- [Redis Docker イメージ](https://hub.docker.com/_/redis)
- [Git公式ドキュメント](https://git-scm.com/doc)

## 🤝 貢献

プルリクエストやIssueを歓迎します！

1. リポジトリをフォーク
2. フィーチャーブランチを作成 (`git checkout -b feature/amazing-feature`)
3. 変更をコミット (`git commit -m 'Add amazing feature'`)
4. ブランチにプッシュ (`git push origin feature/amazing-feature`)
5. プルリクエストを作成

---

**このサンプルを通じて、モダンなDocker環境構築とGitを使ったチーム開発の基礎を身につけましょう！**