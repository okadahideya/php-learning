@extends('layouts.app')

@section('title', 'ダッシュボード')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-tachometer-alt mr-2"></i>ダッシュボード
            </h1>
            <p class="mt-1 text-sm text-gray-600">タスクの概要と最新情報</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Tasks -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-tasks text-2xl text-blue-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">総タスク数</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Tasks -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-2xl text-green-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">完了済み</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress Tasks -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-2xl text-yellow-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">進行中</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['in_progress'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-pie text-2xl text-purple-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">完了率</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $completionRate }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    @if($overdueTasks->count() > 0 || $todayTasks->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Overdue Tasks -->
        @if($overdueTasks->count() > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-red-800 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>期限切れタスク ({{ $overdueTasks->count() }}件)
            </h3>
            <div class="space-y-2">
                @foreach($overdueTasks->take(3) as $task)
                <div class="flex items-center justify-between bg-white p-3 rounded border border-red-200">
                    <div>
                        <a href="{{ route('tasks.show', $task) }}" class="font-medium text-gray-900 hover:text-blue-600">
                            {{ $task->title }}
                        </a>
                        <p class="text-sm text-red-600">期限: {{ $task->due_date->format('Y/m/d') }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                        {{ $task->priority_label }}
                    </span>
                </div>
                @endforeach
                @if($overdueTasks->count() > 3)
                <p class="text-sm text-red-600">他 {{ $overdueTasks->count() - 3 }}件...</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Today's Tasks -->
        @if($todayTasks->count() > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-yellow-800 mb-4">
                <i class="fas fa-calendar-day mr-2"></i>今日期限のタスク ({{ $todayTasks->count() }}件)
            </h3>
            <div class="space-y-2">
                @foreach($todayTasks as $task)
                <div class="flex items-center justify-between bg-white p-3 rounded border border-yellow-200">
                    <div>
                        <a href="{{ route('tasks.show', $task) }}" class="font-medium text-gray-900 hover:text-blue-600">
                            {{ $task->title }}
                        </a>
                        <p class="text-sm text-gray-600">{{ $task->status_label }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                        {{ $task->priority_label }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Recent Tasks & This Week -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Tasks -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-history mr-2"></i>最近のタスク
                </h3>
                @if($recentTasks->count() > 0)
                <div class="space-y-3">
                    @foreach($recentTasks as $task)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded priority-{{ $task->priority }}">
                        <div class="flex-1">
                            <a href="{{ route('tasks.show', $task) }}" class="font-medium text-gray-900 hover:text-blue-600">
                                {{ $task->title }}
                            </a>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded-full">
                                    {{ $task->status_label }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $task->created_at->format('m/d') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500">タスクがありません</p>
                @endif
            </div>
        </div>

        <!-- This Week's Tasks -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-calendar-week mr-2"></i>今週期限のタスク
                </h3>
                @if($thisWeekTasks->count() > 0)
                <div class="space-y-3">
                    @foreach($thisWeekTasks as $task)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded priority-{{ $task->priority }}">
                        <div class="flex-1">
                            <a href="{{ route('tasks.show', $task) }}" class="font-medium text-gray-900 hover:text-blue-600">
                                {{ $task->title }}
                            </a>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded-full">
                                    {{ $task->status_label }}
                                </span>
                                @if($task->due_date)
                                <span class="text-sm text-gray-500">
                                    {{ $task->due_date->format('m/d') }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500">今週期限のタスクはありません</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-bolt mr-2"></i>クイックアクション
            </h3>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('tasks.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">
                    <i class="fas fa-plus mr-2"></i>新しいタスクを作成
                </a>
                <a href="{{ route('tasks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">
                    <i class="fas fa-list mr-2"></i>全タスクを表示
                </a>
            </div>
        </div>
    </div>
</div>
@endsection