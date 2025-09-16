<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * ユーザーが所有するタスクとの関係
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * 完了していないタスクのみ取得
     */
    public function incompleteTasks(): HasMany
    {
        return $this->tasks()->incomplete();
    }

    /**
     * 期限切れのタスクのみ取得
     */
    public function overdueTasks(): HasMany
    {
        return $this->tasks()->overdue();
    }

    /**
     * タスクの統計情報を取得
     */
    public function getTaskStats(): array
    {
        $tasks = $this->tasks;
        
        return [
            'total' => $tasks->count(),
            'completed' => $tasks->where('status', Task::STATUS_COMPLETED)->count(),
            'in_progress' => $tasks->where('status', Task::STATUS_IN_PROGRESS)->count(),
            'todo' => $tasks->where('status', Task::STATUS_TODO)->count(),
            'overdue' => $tasks->filter(fn($task) => $task->isOverdue())->count(),
        ];
    }

    /**
     * 完了率を取得（パーセンテージ）
     */
    public function getCompletionRate(): float
    {
        $total = $this->tasks()->count();
        
        if ($total === 0) {
            return 0;
        }
        
        $completed = $this->tasks()->where('status', Task::STATUS_COMPLETED)->count();
        
        return round(($completed / $total) * 100, 1);
    }
}