# ===== Docker環境管理 Makefile =====
# PHP学習用Docker環境の操作を簡単にするためのMakefile

# デフォルトターゲット
.DEFAULT_GOAL := help

# 変数定義
DOCKER_COMPOSE = docker-compose
PROJECT_NAME = php_learning
MYSQL_ROOT_PASSWORD = root_password
MYSQL_DATABASE = php_learning
MYSQL_USER = php_user
MYSQL_PASSWORD = php_password

# ===== ヘルプ =====
.PHONY: help
help: ## このヘルプメッセージを表示
	@echo "=== PHP学習環境 Docker操作コマンド ==="
	@echo ""
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# ===== 環境構築 =====
.PHONY: setup
setup: ## 初回環境構築（コンテナビルド〜起動）
	@echo "🚀 Docker環境を初期構築中..."
	$(DOCKER_COMPOSE) build --no-cache
	$(DOCKER_COMPOSE) up -d
	@echo "⏳ データベース起動を待機中..."
	sleep 30
	@echo "✅ セットアップ完了！"
	@echo "📋 アクセス情報:"
	@echo "   - Webアプリ: http://localhost:8080"
	@echo "   - phpMyAdmin: http://localhost:8081"
	@echo "   - MailHog: http://localhost:8025"

.PHONY: build
build: ## Dockerイメージをビルド
	@echo "🔨 Dockerイメージをビルド中..."
	$(DOCKER_COMPOSE) build

.PHONY: rebuild
rebuild: ## Dockerイメージを再ビルド（キャッシュなし）
	@echo "🔨 Dockerイメージを再ビルド中（キャッシュなし）..."
	$(DOCKER_COMPOSE) build --no-cache

# ===== コンテナ操作 =====
.PHONY: up
up: ## コンテナを起動
	@echo "🚀 コンテナを起動中..."
	$(DOCKER_COMPOSE) up -d
	@echo "✅ コンテナ起動完了"

.PHONY: down
down: ## コンテナを停止・削除
	@echo "🛑 コンテナを停止中..."
	$(DOCKER_COMPOSE) down
	@echo "✅ コンテナ停止完了"

.PHONY: restart
restart: ## コンテナを再起動
	@echo "🔄 コンテナを再起動中..."
	$(DOCKER_COMPOSE) down
	$(DOCKER_COMPOSE) up -d
	@echo "✅ コンテナ再起動完了"

.PHONY: stop
stop: ## コンテナを停止
	@echo "⏸️  コンテナを停止中..."
	$(DOCKER_COMPOSE) stop
	@echo "✅ コンテナ停止完了"

.PHONY: start
start: ## 停止したコンテナを開始
	@echo "▶️  コンテナを開始中..."
	$(DOCKER_COMPOSE) start
	@echo "✅ コンテナ開始完了"

# ===== 状態確認 =====
.PHONY: status
status: ## コンテナの状態を表示
	@echo "📊 コンテナ状態:"
	$(DOCKER_COMPOSE) ps

.PHONY: logs
logs: ## 全コンテナのログを表示
	$(DOCKER_COMPOSE) logs -f

.PHONY: logs-web
logs-web: ## Webサーバーのログを表示
	$(DOCKER_COMPOSE) logs -f web

.PHONY: logs-app
logs-app: ## PHPアプリのログを表示
	$(DOCKER_COMPOSE) logs -f app

.PHONY: logs-db
logs-db: ## データベースのログを表示
	$(DOCKER_COMPOSE) logs -f database

.PHONY: logs-redis
logs-redis: ## Redisのログを表示
	$(DOCKER_COMPOSE) logs -f redis

# ===== コンテナ接続 =====
.PHONY: shell-app
shell-app: ## PHPアプリコンテナにシェル接続
	$(DOCKER_COMPOSE) exec app sh

.PHONY: shell-web
shell-web: ## Webサーバーコンテナにシェル接続
	$(DOCKER_COMPOSE) exec web sh

.PHONY: shell-db
shell-db: ## データベースコンテナにシェル接続
	$(DOCKER_COMPOSE) exec database bash

.PHONY: mysql
mysql: ## MySQLクライアントでデータベースに接続
	$(DOCKER_COMPOSE) exec database mysql -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(MYSQL_DATABASE)

.PHONY: mysql-root
mysql-root: ## MySQLにrootユーザーで接続
	$(DOCKER_COMPOSE) exec database mysql -uroot -p$(MYSQL_ROOT_PASSWORD)

.PHONY: redis-cli
redis-cli: ## Redis CLIに接続
	$(DOCKER_COMPOSE) exec redis redis-cli

# ===== 開発者向けツール =====
.PHONY: composer-install
composer-install: ## Composer依存関係をインストール
	$(DOCKER_COMPOSE) exec composer composer install

.PHONY: composer-update
composer-update: ## Composer依存関係を更新
	$(DOCKER_COMPOSE) exec composer composer update

.PHONY: composer-dump
composer-dump: ## Composerオートロードを再生成
	$(DOCKER_COMPOSE) exec composer composer dump-autoload

.PHONY: npm-install
npm-install: ## npm依存関係をインストール
	$(DOCKER_COMPOSE) exec node npm install

.PHONY: npm-dev
npm-dev: ## npm開発サーバーを起動
	$(DOCKER_COMPOSE) exec node npm run dev

.PHONY: npm-build
npm-build: ## フロントエンドをビルド
	$(DOCKER_COMPOSE) exec node npm run build

# ===== データベース操作 =====
.PHONY: db-seed
db-seed: ## サンプルデータを追加投入
	@echo "🌱 サンプルデータを投入中..."
	$(DOCKER_COMPOSE) exec database mysql -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(MYSQL_DATABASE) < ./docker/mysql/init/01_create_database.sql
	@echo "✅ サンプルデータ投入完了"

.PHONY: db-backup
db-backup: ## データベースをバックアップ
	@echo "💾 データベースをバックアップ中..."
	mkdir -p backups
	$(DOCKER_COMPOSE) exec database mysqldump -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(MYSQL_DATABASE) > backups/backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "✅ バックアップ完了: backups/backup_$(shell date +%Y%m%d_%H%M%S).sql"

.PHONY: db-restore
db-restore: ## データベースを復元（backup_file=path/to/backup.sql）
	@if [ -z "$(backup_file)" ]; then echo "❌ backup_file パラメータが必要です。例: make db-restore backup_file=backups/backup.sql"; exit 1; fi
	@echo "📥 データベースを復元中..."
	$(DOCKER_COMPOSE) exec -T database mysql -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(MYSQL_DATABASE) < $(backup_file)
	@echo "✅ データベース復元完了"

# ===== テスト =====
.PHONY: test
test: ## PHPUnitテストを実行
	$(DOCKER_COMPOSE) exec app vendor/bin/phpunit

.PHONY: test-coverage
test-coverage: ## カバレッジ付きでテストを実行
	$(DOCKER_COMPOSE) exec app vendor/bin/phpunit --coverage-html coverage

# ===== クリーンアップ =====
.PHONY: clean
clean: ## 停止したコンテナとボリュームを削除
	@echo "🧹 クリーンアップ中..."
	$(DOCKER_COMPOSE) down -v
	docker system prune -f
	@echo "✅ クリーンアップ完了"

.PHONY: clean-all
clean-all: ## 全てのコンテナ・イメージ・ボリュームを削除
	@echo "🧹 完全クリーンアップ中（注意：全データが削除されます）..."
	@read -p "本当に削除しますか？ (y/N): " confirm && [ "$$confirm" = "y" ]
	$(DOCKER_COMPOSE) down -v --rmi all
	docker system prune -af --volumes
	@echo "✅ 完全クリーンアップ完了"

.PHONY: clean-logs
clean-logs: ## ログファイルを削除
	@echo "🗑️  ログファイルを削除中..."
	$(DOCKER_COMPOSE) exec app rm -rf /var/log/*.log
	@echo "✅ ログファイル削除完了"

# ===== 監視・デバッグ =====
.PHONY: top
top: ## コンテナのリソース使用状況を表示
	docker stats $(shell $(DOCKER_COMPOSE) ps -q)

.PHONY: inspect-app
inspect-app: ## PHPアプリコンテナの詳細情報を表示
	$(DOCKER_COMPOSE) exec app php -m | head -20
	@echo "--- PHP設定 ---"
	$(DOCKER_COMPOSE) exec app php -i | grep -E "(memory_limit|max_execution_time|upload_max_filesize)"

.PHONY: inspect-db
inspect-db: ## データベースの状態を確認
	$(DOCKER_COMPOSE) exec database mysql -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) -e "SELECT VERSION(); SHOW DATABASES; SHOW PROCESSLIST;"

.PHONY: inspect-redis
inspect-redis: ## Redisの状態を確認
	$(DOCKER_COMPOSE) exec redis redis-cli info server | head -10
	$(DOCKER_COMPOSE) exec redis redis-cli info memory | grep used_memory_human

# ===== セキュリティ =====
.PHONY: security-scan
security-scan: ## セキュリティスキャンを実行
	@echo "🔒 セキュリティスキャン実行中..."
	$(DOCKER_COMPOSE) exec composer composer audit
	@echo "✅ セキュリティスキャン完了"

# ===== プロダクション準備 =====
.PHONY: prod-build
prod-build: ## プロダクション用ビルド
	@echo "🏭 プロダクション用ビルド中..."
	$(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.prod.yml build
	@echo "✅ プロダクション用ビルド完了"

# ===== 便利コマンド =====
.PHONY: open
open: ## ブラウザで各サービスを開く
	@echo "🌐 ブラウザでサービスを開いています..."
	@if command -v open >/dev/null 2>&1; then \
		open http://localhost:8080 && \
		open http://localhost:8081 && \
		open http://localhost:8025; \
	elif command -v xdg-open >/dev/null 2>&1; then \
		xdg-open http://localhost:8080 && \
		xdg-open http://localhost:8081 && \
		xdg-open http://localhost:8025; \
	else \
		echo "ブラウザを手動で開いてください:"; \
		echo "  - Webアプリ: http://localhost:8080"; \
		echo "  - phpMyAdmin: http://localhost:8081"; \
		echo "  - MailHog: http://localhost:8025"; \
	fi

.PHONY: urls
urls: ## アクセス可能なURLを表示
	@echo "📋 アクセス可能なURL:"
	@echo "  🌐 Webアプリケーション: http://localhost:8080"
	@echo "  🗄️  phpMyAdmin: http://localhost:8081"
	@echo "  📧 MailHog: http://localhost:8025"
	@echo "  🔧 直接接続:"
	@echo "    - MySQL: localhost:3306"
	@echo "    - Redis: localhost:6379"

# 定義されていないターゲットに対するエラーハンドリング
%:
	@echo "❌ 不明なコマンド: $@"
	@echo "利用可能なコマンドを確認するには 'make help' を実行してください"