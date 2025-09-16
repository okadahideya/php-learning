<div class="bg-gray-50 rounded-lg p-4 border priority-{{ $task->priority }} hover:shadow-md transition-shadow">
    <!-- Task Header -->
    <div class="flex justify-between items-start mb-2">
        <h4 class="font-medium text-gray-900 flex-1">
            <a href="{{ route('tasks.show', $task) }}" class="hover:text-blue-600">
                {{ $task->title }}
            </a>
        </h4>
        
        <!-- Priority Badge -->
        <span class="ml-2 px-2 py-1 text-xs rounded-full
            {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}
            {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
            {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
            {{ $task->priority_label }}
        </span>
    </div>

    <!-- Task Description -->
    @if($task->description)
    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
        {{ Str::limit($task->description, 80) }}
    </p>
    @endif

    <!-- Due Date -->
    @if($task->due_date)
    <div class="flex items-center mb-3 text-sm">
        <i class="fas fa-calendar mr-1 
            {{ $task->isOverdue() ? 'text-red-500' : 'text-gray-400' }}"></i>
        <span class="{{ $task->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
            {{ $task->due_date->format('Y/m/d') }}
            @if($task->isOverdue())
                <span class="text-red-500">(期限切れ)</span>
            @endif
        </span>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex justify-between items-center">
        <!-- Status Change Buttons -->
        <div class="flex space-x-1">
            @if($task->status !== 'todo')
            <button onclick="changeTaskStatus({{ $task->id }}, 'todo')" 
                    class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200">
                <i class="fas fa-undo"></i>
            </button>
            @endif
            
            @if($task->status !== 'in_progress')
            <button onclick="changeTaskStatus({{ $task->id }}, 'in_progress')" 
                    class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                <i class="fas fa-play"></i>
            </button>
            @endif
            
            @if($task->status !== 'completed')
            <button onclick="changeTaskStatus({{ $task->id }}, 'completed')" 
                    class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                <i class="fas fa-check"></i>
            </button>
            @endif
        </div>

        <!-- Edit/Delete Buttons -->
        <div class="flex space-x-1">
            <a href="{{ route('tasks.edit', $task) }}" 
               class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200">
                <i class="fas fa-edit"></i>
            </a>
            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline"
                  onsubmit="return confirm('このタスクを削除しますか？')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Created At -->
    <div class="mt-2 text-xs text-gray-400">
        作成: {{ $task->created_at->format('m/d H:i') }}
    </div>
</div>