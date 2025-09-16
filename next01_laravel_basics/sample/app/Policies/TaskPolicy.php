<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * タスクを表示できるかどうか
     */
    public function view(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    /**
     * タスクを作成できるかどうか
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * タスクを更新できるかどうか
     */
    public function update(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    /**
     * タスクを削除できるかどうか
     */
    public function delete(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }
}