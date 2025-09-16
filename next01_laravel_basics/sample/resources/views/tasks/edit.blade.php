@extends('layouts.app')

@section('title', 'タスクを編集')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-edit mr-2"></i>タスクを編集
            </h1>
            <p class="mt-1 text-sm text-gray-600">タスクの詳細を変更してください</p>
        </div>
    </div>

    <!-- Task Form -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <form method="POST" action="{{ route('tasks.update', $task) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-heading mr-1"></i>タスク名 <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title"
                       value="{{ old('title', $task->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="例: プロジェクトの企画書を作成する"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-1"></i>説明
                </label>
                <textarea name="description" 
                          id="description"
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="タスクの詳細な説明を入力してください（任意）">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status, Priority and Due Date Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tasks mr-1"></i>ステータス <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        @foreach(\App\Models\Task::getStatuses() as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $task->status) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag mr-1"></i>優先度 <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" 
                            id="priority"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        @foreach(\App\Models\Task::getPriorities() as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', $task->priority) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1"></i>期限
                    </label>
                    <input type="date" 
                           name="due_date" 
                           id="due_date"
                           value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Current Status Display -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">現在の状態</h3>
                <div class="flex items-center space-x-4 text-sm">
                    <span class="flex items-center">
                        <i class="fas fa-flag mr-1 text-gray-400"></i>
                        優先度: 
                        <span class="ml-1 px-2 py-1 rounded-full text-xs
                            {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $task->priority_label }}
                        </span>
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-tasks mr-1 text-gray-400"></i>
                        ステータス: 
                        <span class="ml-1 px-2 py-1 rounded-full text-xs
                            {{ $task->status === 'todo' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                            {{ $task->status_label }}
                        </span>
                    </span>
                    @if($task->due_date)
                    <span class="flex items-center">
                        <i class="fas fa-calendar mr-1 text-gray-400"></i>
                        期限: 
                        <span class="ml-1 {{ $task->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                            {{ $task->due_date->format('Y/m/d') }}
                            @if($task->isOverdue())
                                <span class="text-red-500">(期限切れ)</span>
                            @endif
                        </span>
                    </span>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <div class="flex space-x-3">
                    <a href="{{ route('tasks.show', $task) }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-times mr-1"></i>キャンセル
                    </a>
                    <a href="{{ route('tasks.index') }}" 
                       class="px-4 py-2 text-blue-600 hover:text-blue-800 transition">
                        <i class="fas fa-list mr-1"></i>一覧に戻る
                    </a>
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                    <i class="fas fa-save mr-1"></i>変更を保存
                </button>
            </div>
        </form>
    </div>

    <!-- Help Text -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-medium text-blue-800 mb-2">
            <i class="fas fa-info-circle mr-1"></i>編集のヒント
        </h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• ステータスを変更してタスクの進捗を管理しましょう</li>
            <li>• 優先度を適切に設定して効率的に作業しましょう</li>
            <li>• 期限を削除したい場合は日付欄を空白にしてください</li>
            <li>• 変更はすぐに反映されます</li>
        </ul>
    </div>
</div>
@endsection