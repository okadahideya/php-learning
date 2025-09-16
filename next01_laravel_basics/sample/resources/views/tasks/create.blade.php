@extends('layouts.app')

@section('title', '新しいタスクを作成')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-plus mr-2"></i>新しいタスクを作成
            </h1>
            <p class="mt-1 text-sm text-gray-600">新しいタスクの詳細を入力してください</p>
        </div>
    </div>

    <!-- Task Form -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <form method="POST" action="{{ route('tasks.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-heading mr-1"></i>タスク名 <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title"
                       value="{{ old('title') }}"
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
                          placeholder="タスクの詳細な説明を入力してください（任意）">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Priority and Due Date Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            <option value="{{ $value }}" {{ old('priority', 'medium') === $value ? 'selected' : '' }}>
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
                           value="{{ old('due_date') }}"
                           min="{{ now()->addDay()->format('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">期限を設定しない場合は空白のままにしてください</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('tasks.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-1"></i>キャンセル
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                    <i class="fas fa-save mr-1"></i>タスクを作成
                </button>
            </div>
        </form>
    </div>

    <!-- Help Text -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-medium text-blue-800 mb-2">
            <i class="fas fa-info-circle mr-1"></i>タスク作成のヒント
        </h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• タスク名は具体的で分かりやすくしましょう</li>
            <li>• 優先度を設定して効率的にタスクを管理しましょう</li>
            <li>• 期限がある場合は必ず設定しておきましょう</li>
            <li>• 説明欄には作業の詳細や注意点を記載しましょう</li>
        </ul>
    </div>
</div>
@endsection