<?php

namespace PhpLearning\Testing;

/**
 * データベースインターフェース - PHPUnitテスト学習用サンプル
 * データベース操作を抽象化するためのインターフェース
 */
interface DatabaseInterface
{
    /**
     * データを挿入
     * 
     * @param string $table テーブル名
     * @param array $data 挿入するデータ
     * @return bool 成功した場合 true
     */
    public function insert(string $table, array $data): bool;

    /**
     * データを検索（単一）
     * 
     * @param string $table テーブル名
     * @param array $conditions 検索条件
     * @return array|null 見つからない場合は null
     */
    public function find(string $table, array $conditions): ?array;

    /**
     * 全データを取得
     * 
     * @param string $table テーブル名
     * @return array データの配列
     */
    public function findAll(string $table): array;

    /**
     * データを更新
     * 
     * @param string $table テーブル名
     * @param array $conditions 更新条件
     * @param array $data 更新するデータ
     * @return bool 成功した場合 true
     */
    public function update(string $table, array $conditions, array $data): bool;

    /**
     * データを削除
     * 
     * @param string $table テーブル名
     * @param array $conditions 削除条件
     * @return bool 成功した場合 true
     */
    public function delete(string $table, array $conditions): bool;

    /**
     * レコード数を取得
     * 
     * @param string $table テーブル名
     * @param array $conditions 条件（オプション）
     * @return int レコード数
     */
    public function count(string $table, array $conditions = []): int;

    /**
     * 複数条件でデータを検索
     * 
     * @param string $table テーブル名
     * @param array $conditions 検索条件
     * @return array 検索結果の配列
     */
    public function findWhere(string $table, array $conditions): array;

    /**
     * トランザクション開始
     */
    public function beginTransaction(): void;

    /**
     * コミット
     */
    public function commit(): void;

    /**
     * ロールバック
     */
    public function rollback(): void;
}