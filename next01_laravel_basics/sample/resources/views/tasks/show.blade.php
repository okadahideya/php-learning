@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        {{ $task->title }}
                    </h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span>
                            <i class="fas fa-calendar mr-1"></i>
                            作成: {{ $task->created_at->format('Y年m月d日 H:i') }}
                        </span>
                        @if($task->updated_at->ne($task->created_at))
                        <span>
                            <i class="fas fa-edit mr-1"></i>
                            更新: {{ $task->updated_at->format('Y年m月d日 H:i') }}
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <a href="{{ route('tasks.edit', $task) }}" 
                       class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">
                        <i class="fas fa-edit mr-1"></i>編集
                    </a>
                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline"
                          onsubmit="return confirm('このタスクを削除しますか？削除したタスクは復元できません。')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">
                            <i class="fas fa-trash mr-1"></i>削除
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-align-left mr-2"></i>説明
                    </h2>
                    @if($task->description)
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-line">{{ $task->description }}</p>
                        </div>
                    @else
                        <p class="text-gray-500 italic">説明はありません</p>
                    @endif
                </div>
            </div>

            <!-- Status Change Actions -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-tasks mr-2"></i>ステータス変更
                    </h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach(\App\Models\Task::getStatuses() as $status => $label)
                            @if($task->status !== $status)
                                <button onclick="changeTaskStatus({{ $task->id }}, '{{ $status }}')" 
                                        class="px-4 py-2 rounded-md transition
                                        {{ $status === 'todo' ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : '' }}
                                        {{ $status === 'in_progress' ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' : '' }}
                                        {{ $status === 'completed' ? 'bg-green-100 text-green-700 hover:bg-green-200' : '' }}">
                                    <i class="fas 
                                        {{ $status === 'todo' ? 'fa-undo' : '' }}
                                        {{ $status === 'in_progress' ? 'fa-play' : '' }}
                                        {{ $status === 'completed' ? 'fa-check' : '' }} mr-1"></i>
                                    {{ $label }}に変更
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Task Properties -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>タスク情報
                    </h2>
                    <dl class="space-y-4">
                        <!-- Status -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ステータス</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $task->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                                    <i class="fas 
                                        {{ $task->status === 'todo' ? 'fa-circle' : '' }}
                                        {{ $task->status === 'in_progress' ? 'fa-clock' : '' }}
                                        {{ $task->status === 'completed' ? 'fa-check-circle' : '' }} mr-1"></i>
                                    {{ $task->status_label }}
                                </span>
                            </dd>
                        </div>

                        <!-- Priority -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">優先度</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}">
                                    <i class="fas fa-flag mr-1"></i>
                                    {{ $task->priority_label }}
                                </span>
                            </dd>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">期限</dt>
                            <dd class="mt-1">
                                @if($task->due_date)
                                    <div class="flex items-center {{ $task->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                                        <i class="fas fa-calendar mr-2"></i>
                                        <span>{{ $task->due_date->format('Y年m月d日') }}</span>
                                        @if($task->isOverdue())
                                            <span class="ml-2 text-red-500 font-medium">(期限切れ)</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $task->due_date->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-gray-500">設定されていません</span>
                                @endif
                            </dd>
                        </div>

                        <!-- Owner -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">作成者</dt>
                            <dd class="mt-1 flex items-center">
                                <i class="fas fa-user mr-2 text-gray-400"></i>
                                <span class="text-gray-900">{{ $task->user->name }}</span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Navigation -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-navigation mr-2"></i>ナビゲーション
                    </h2>
                    <div class="space-y-2">
                        <a href="{{ route('tasks.index') }}" 
                           class="flex items-center text-blue-600 hover:text-blue-800">
                            <i class="fas fa-list mr-2"></i>タスク一覧に戻る
                        </a>
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center text-blue-600 hover:text-blue-800">
                            <i class="fas fa-tachometer-alt mr-2"></i>ダッシュボードに戻る
                        </a>
                        <a href="{{ route('tasks.create') }}" 
                           class="flex items-center text-blue-600 hover:text-blue-800">
                            <i class="fas fa-plus mr-2"></i>新しいタスクを作成
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function changeTaskStatus(taskId, newStatus) {
    if (confirm('ステータスを変更しますか？')) {
        updateTaskStatus(taskId, newStatus);
    }
}
</script>
@endpush
@endsection