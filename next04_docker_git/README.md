# Next Step 04: Docker & Git 開発環境

## 学習目標
- Dockerコンテナ技術の基本を理解する
- 開発環境のコンテナ化を実践する
- Gitによるバージョン管理を習得する
- チーム開発に必要なワークフローを学ぶ

## 前提条件
- PHP、MySQL基礎学習完了
- ターミナル操作の基本知識
- テキストエディタの基本操作

## 環境構築

### Docker のインストール

#### Windows/macOS
[Docker Desktop](https://www.docker.com/products/docker-desktop) をダウンロード・インストール

#### Linux (Ubuntu)
```bash
# 依存関係インストール
sudo apt update
sudo apt install apt-transport-https ca-certificates curl software-properties-common

# Docker公式GPGキー追加
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -

# Dockerリポジトリ追加
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"

# Dockerインストール
sudo apt update
sudo apt install docker-ce

# ユーザーをdockerグループに追加
sudo usermod -aG docker $USER
```

### Git のインストール

#### macOS
```bash
# Homebrewでインストール
brew install git

# または Xcodeコマンドラインツール
xcode-select --install
```

#### Windows
[Git公式サイト](https://git-scm.com/) からダウンロード

#### Linux
```bash
sudo apt update
sudo apt install git
```

## Docker基礎

### 基本概念
- **イメージ**: アプリケーションの設計図
- **コンテナ**: 実行中のアプリケーション
- **Dockerfile**: イメージ作成の設定ファイル
- **docker-compose**: 複数コンテナの管理

### 基本コマンド
```bash
# バージョン確認
docker --version
docker-compose --version

# イメージ一覧
docker images

# コンテナ一覧
docker ps           # 実行中のみ
docker ps -a        # 全て

# コンテナ実行
docker run -it ubuntu:20.04 bash

# コンテナ停止・削除
docker stop container_name
docker rm container_name

# イメージ削除
docker rmi image_name
```

## PHP開発環境のコンテナ化

### Dockerfile の作成
```dockerfile
# sample/Dockerfile
FROM php:8.2-apache

# 必要な拡張機能をインストール
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Apacheの設定
RUN a2enmod rewrite

# Composerインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリ設定
WORKDIR /var/www/html

# ファイルコピー
COPY . /var/www/html/

# 権限設定
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80
```

### docker-compose.yml
```yaml
# sample/docker-compose.yml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_NAME=app_db
      - DB_USER=app_user
      - DB_PASS=secure_pass

  db:
    image: mysql:8.0
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: app_db
      MYSQL_USER: app_user
      MYSQL_PASSWORD: secure_pass
    volumes:
      - mysql_data:/var/lib/mysql
      - ./sql:/docker-entrypoint-initdb.d

  phpmyadmin:
    image: phpmyadmin:latest
    ports:
      - "8081:80"
    environment:
      PMA_HOST: db
      PMA_USER: app_user
      PMA_PASSWORD: secure_pass
    depends_on:
      - db

volumes:
  mysql_data:
```

### 実行方法
```bash
# コンテナ起動
docker-compose up -d

# ログ確認
docker-compose logs

# コンテナ内でコマンド実行
docker-compose exec web bash
docker-compose exec db mysql -u app_user -p

# コンテナ停止・削除
docker-compose down
docker-compose down -v  # ボリュームも削除
```

## Git基礎

### 初期設定
```bash
# 個人情報設定
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# エディタ設定
git config --global core.editor "code --wait"  # VSCode使用の場合

# 設定確認
git config --list
```

### 基本的なワークフロー
```bash
# リポジトリ初期化
git init

# ファイル追加
git add .                    # 全ファイル
git add filename.php         # 特定ファイル
git add *.php               # パターンマッチ

# コミット
git commit -m "Initial commit"
git commit -m "Add user authentication feature"

# 状態確認
git status                  # 作業ディレクトリの状態
git log                     # コミット履歴
git log --oneline          # 簡潔な履歴

# 差分確認
git diff                    # 作業ディレクトリとステージング
git diff --staged          # ステージングとコミット
git diff HEAD~1            # 前回コミットとの差分
```

### ブランチ操作
```bash
# ブランチ一覧
git branch

# 新しいブランチ作成・切り替え
git checkout -b feature/user-profile
git switch -c feature/user-profile  # 新しいコマンド

# ブランチ切り替え
git checkout main
git switch main

# ブランチマージ
git checkout main
git merge feature/user-profile

# ブランチ削除
git branch -d feature/user-profile
```

### リモートリポジトリ
```bash
# リモート追加
git remote add origin https://github.com/username/repository.git

# プッシュ
git push origin main
git push origin feature/user-profile

# プル
git pull origin main

# クローン
git clone https://github.com/username/repository.git
```

## 実用的な.gitignore
```gitignore
# .gitignore

# PHP
/vendor/
composer.lock
*.log

#環境設定
.env
.env.local

# IDE
.vscode/
.idea/
*.swp
*.swo

# OS
.DS_Store
Thumbs.db

# Docker
.docker/
docker-compose.override.yml

# 一時ファイル
/tmp/
/cache/
/uploads/

# データベース
*.sqlite
*.db

# Node.js (フロントエンド開発時)
node_modules/
npm-debug.log
yarn-error.log
```

## 実装課題

### 課題1: 基本的な開発環境構築
1. PHP + MySQL のDockerコンテナ作成
2. docker-compose での環境管理
3. 開発に必要なツールの追加

### 課題2: Gitによるバージョン管理
1. プロジェクトのGit管理開始
2. 機能別ブランチでの開発
3. 適切なコミットメッセージ作成

### 課題3: チーム開発シミュレーション
1. リモートリポジトリの使用
2. ブランチ戦略の実践
3. コンフリクト解決

## Docker実践例

### Laravel開発環境
```yaml
# laravel-docker-compose.yml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.laravel
    volumes:
      - .:/var/www/html
    ports:
      - "8000:8000"
    depends_on:
      - db
      - redis

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: password
      MYSQL_DATABASE: laravel
      MYSQL_USER: laravel
      MYSQL_PASSWORD: password
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

  mailhog:
    image: mailhog/mailhog:latest
    ports:
      - "8025:8025"

volumes:
  mysql_data:
```

### Node.js付きPHP環境
```dockerfile
# Dockerfile.fullstack
FROM php:8.2-fpm

# Node.js インストール
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

# PHP拡張機能
RUN docker-php-ext-install pdo pdo_mysql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリ
WORKDIR /var/www/html

# npm, composer両方使用可能
CMD ["php-fpm"]
```

## チーム開発ワークフロー

### Git Flow
```bash
# メインブランチ
main (production)
develop (development)

# フィーチャーブランチ
git checkout develop
git checkout -b feature/user-authentication
# 開発作業
git push origin feature/user-authentication
# プルリクエスト作成

# リリースブランチ
git checkout develop
git checkout -b release/v1.0.0
# リリース準備
git checkout main
git merge release/v1.0.0
git tag v1.0.0

# ホットフィックス
git checkout main
git checkout -b hotfix/critical-bug
# 修正作業
git checkout main
git merge hotfix/critical-bug
```

### 便利なGitエイリアス
```bash
# ~/.gitconfig に追加
[alias]
    st = status
    co = checkout
    br = branch
    ci = commit
    df = diff
    lg = log --oneline --graph --decorate --all
    unstage = reset HEAD --
    last = log -1 HEAD
```

## トラブルシューティング

### Docker関連
```bash
# コンテナが起動しない
docker-compose logs service_name

# 権限エラー
docker-compose exec web chown -R www-data:www-data /var/www/html

# ポート競合
docker-compose down
lsof -i :8080  # ポート使用状況確認

# イメージ・コンテナ削除
docker system prune -a  # 未使用リソース一括削除
```

### Git関連
```bash
# コミット取り消し
git reset --soft HEAD~1    # コミットのみ取り消し
git reset --hard HEAD~1    # ファイル変更も取り消し

# 間違ったブランチにコミット
git log --oneline
git checkout correct-branch
git cherry-pick commit-hash

# リモートリポジトリURL変更
git remote set-url origin new-repository-url
```

## 次のステップ
Docker/Git をマスターしたら、最後にテスト駆動開発（TDD）を学習しましょう！