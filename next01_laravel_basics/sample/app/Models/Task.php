<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * タスクの所有者（ユーザー）との関係
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ステータスの定数定義
     */
    const STATUS_TODO = 'todo';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    /**
     * 優先度の定数定義
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    /**
     * 利用可能なステータス一覧を取得
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_TODO => 'やること',
            self::STATUS_IN_PROGRESS => '進行中',
            self::STATUS_COMPLETED => '完了',
        ];
    }

    /**
     * 利用可能な優先度一覧を取得
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => '低',
            self::PRIORITY_MEDIUM => '中',
            self::PRIORITY_HIGH => '高',
        ];
    }

    /**
     * ステータスの日本語表示を取得
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * 優先度の日本語表示を取得
     */
    public function getPriorityLabelAttribute(): string
    {
        return self::getPriorities()[$this->priority] ?? $this->priority;
    }

    /**
     * 完了しているかどうか
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * 期限切れかどうか
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->isCompleted();
    }

    /**
     * スコープ: 未完了のタスクのみ
     */
    public function scopeIncomplete($query)
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED);
    }

    /**
     * スコープ: 期限切れのタスクのみ
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->where('status', '!=', self::STATUS_COMPLETED);
    }
}