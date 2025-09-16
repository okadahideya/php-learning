<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    /**
     * タスク一覧表示
     */
    public function index(): View
    {
        $user = auth()->user();
        
        // ステータス別にタスクを取得
        $tasks = [
            'todo' => $user->tasks()->where('status', Task::STATUS_TODO)->latest()->get(),
            'in_progress' => $user->tasks()->where('status', Task::STATUS_IN_PROGRESS)->latest()->get(),
            'completed' => $user->tasks()->where('status', Task::STATUS_COMPLETED)->latest()->get(),
        ];
        
        // 統計情報を取得
        $stats = $user->getTaskStats();
        
        return view('tasks.index', compact('tasks', 'stats'));
    }

    /**
     * タスク作成フォーム表示
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * タスク作成処理
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority' => 'required|in:' . implode(',', array_keys(Task::getPriorities())),
            'due_date' => 'nullable|date|after:today',
        ]);

        auth()->user()->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'] ?? null,
            'status' => Task::STATUS_TODO,
        ]);

        return redirect()->route('tasks.index')
                         ->with('success', 'タスクを作成しました。');
    }

    /**
     * タスク詳細表示
     */
    public function show(Task $task): View
    {
        // 自分のタスクかチェック
        $this->authorize('view', $task);
        
        return view('tasks.show', compact('task'));
    }

    /**
     * タスク編集フォーム表示
     */
    public function edit(Task $task): View
    {
        // 自分のタスクかチェック
        $this->authorize('update', $task);
        
        return view('tasks.edit', compact('task'));
    }

    /**
     * タスク更新処理
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        // 自分のタスクかチェック
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:' . implode(',', array_keys(Task::getStatuses())),
            'priority' => 'required|in:' . implode(',', array_keys(Task::getPriorities())),
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.show', $task)
                         ->with('success', 'タスクを更新しました。');
    }

    /**
     * タスク削除処理
     */
    public function destroy(Task $task): RedirectResponse
    {
        // 自分のタスクかチェック
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
                         ->with('success', 'タスクを削除しました。');
    }

    /**
     * ステータス更新（Ajax用）
     */
    public function updateStatus(Request $request, Task $task)
    {
        // 自分のタスクかチェック
        $this->authorize('update', $task);

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Task::getStatuses())),
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'ステータスを更新しました。',
            'task' => $task->fresh(),
        ]);
    }
}