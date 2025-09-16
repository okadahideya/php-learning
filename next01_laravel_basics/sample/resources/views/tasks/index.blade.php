@extends('layouts.app')

@section('title', 'タスク一覧')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-list mr-2"></i>タスク一覧
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">すべてのタスクをステータス別に管理</p>
                </div>
                <a href="{{ route('tasks.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    <i class="fas fa-plus mr-2"></i>新規作成
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <i class="fas fa-circle text-gray-400 mr-2"></i>
                <span class="text-sm font-medium text-gray-600">やること</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ count($tasks['todo']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <i class="fas fa-circle text-blue-400 mr-2"></i>
                <span class="text-sm font-medium text-gray-600">進行中</span>
            </div>
            <p class="text-2xl font-bold text-blue-600">{{ count($tasks['in_progress']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <i class="fas fa-circle text-green-400 mr-2"></i>
                <span class="text-sm font-medium text-gray-600">完了</span>
            </div>
            <p class="text-2xl font-bold text-green-600">{{ count($tasks['completed']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <i class="fas fa-percentage mr-2 text-purple-400"></i>
                <span class="text-sm font-medium text-gray-600">完了率</span>
            </div>
            <p class="text-2xl font-bold text-purple-600">
                {{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0 }}%
            </p>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- TODO Column -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-circle text-gray-400 mr-2"></i>やること
                    <span class="ml-2 bg-gray-100 text-gray-800 text-sm px-2 py-1 rounded-full">
                        {{ count($tasks['todo']) }}
                    </span>
                </h3>
            </div>
            <div class="p-4 space-y-3 min-h-96">
                @foreach($tasks['todo'] as $task)
                    @include('tasks.partials.task-card', ['task' => $task])
                @endforeach
                @if(count($tasks['todo']) === 0)
                    <p class="text-gray-500 text-center py-8">タスクがありません</p>
                @endif
            </div>
        </div>

        <!-- IN PROGRESS Column -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-circle text-blue-400 mr-2"></i>進行中
                    <span class="ml-2 bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded-full">
                        {{ count($tasks['in_progress']) }}
                    </span>
                </h3>
            </div>
            <div class="p-4 space-y-3 min-h-96">
                @foreach($tasks['in_progress'] as $task)
                    @include('tasks.partials.task-card', ['task' => $task])
                @endforeach
                @if(count($tasks['in_progress']) === 0)
                    <p class="text-gray-500 text-center py-8">タスクがありません</p>
                @endif
            </div>
        </div>

        <!-- COMPLETED Column -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-circle text-green-400 mr-2"></i>完了
                    <span class="ml-2 bg-green-100 text-green-800 text-sm px-2 py-1 rounded-full">
                        {{ count($tasks['completed']) }}
                    </span>
                </h3>
            </div>
            <div class="p-4 space-y-3 min-h-96">
                @foreach($tasks['completed'] as $task)
                    @include('tasks.partials.task-card', ['task' => $task])
                @endforeach
                @if(count($tasks['completed']) === 0)
                    <p class="text-gray-500 text-center py-8">タスクがありません</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Status change functionality
function changeTaskStatus(taskId, newStatus) {
    if (confirm('ステータスを変更しますか？')) {
        updateTaskStatus(taskId, newStatus);
    }
}
</script>
@endpush
@endsection