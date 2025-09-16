<?php
/**
 * Docker環境サンプルアプリケーション - エントリーポイント
 * Nginx設定に合わせたpublicディレクトリのindex.php
 */

// 一つ上のディレクトリのindex.phpを読み込み
require_once dirname(__DIR__) . '/index.php';