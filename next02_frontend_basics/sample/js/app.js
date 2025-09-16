// ===== タスク管理アプリケーション =====

// グローバル変数
let tasks = [
    {
        id: 1,
        title: 'プロジェクト企画書の作成',
        description: '新規プロジェクトの企画書を作成する',
        status: 'todo',
        priority: 'high',
        dueDate: '2024-02-15',
        createdAt: new Date('2024-01-15')
    },
    {
        id: 2,
        title: 'データベース設計',
        description: 'ユーザー管理システムのDB設計',
        status: 'in_progress',
        priority: 'medium',
        dueDate: '2024-02-10',
        createdAt: new Date('2024-01-10')
    },
    {
        id: 3,
        title: 'UI/UXデザイン',
        description: 'メインページのデザイン作成',
        status: 'completed',
        priority: 'medium',
        dueDate: '2024-01-30',
        createdAt: new Date('2024-01-05')
    }
];

let currentTaskId = null;
let taskChart = null;
let isDarkMode = false;

// ===== 初期化 =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('TaskManager Frontend Sample 読み込み完了');
    
    // 初期データ表示
    displayTasks();
    updateStatistics();
    initializeChart();
    
    // イベントリスナー設定
    setupEventListeners();
    
    // ローカルストレージからデータ読み込み
    loadFromLocalStorage();
});

// ===== イベントリスナー設定 =====
function setupEventListeners() {
    // タスク追加ボタン
    document.getElementById('addTaskBtn').addEventListener('click', openTaskModal);
    
    // モーダル関連
    document.getElementById('closeModal').addEventListener('click', closeTaskModal);
    document.getElementById('cancelBtn').addEventListener('click', closeTaskModal);
    document.getElementById('taskForm').addEventListener('submit', saveTask);
    
    // フィルター
    document.getElementById('priorityFilter').addEventListener('change', applyFilters);
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    
    // その他のボタン
    document.getElementById('darkModeToggle').addEventListener('click', toggleDarkMode);
    document.getElementById('exportBtn').addEventListener('click', exportTasks);
    
    // モーダル外クリックで閉じる
    document.getElementById('taskModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeTaskModal();
        }
    });
}

// ===== タスク表示関連 =====
function displayTasks() {
    const filteredTasks = getFilteredTasks();
    
    // 各カラムをクリア
    document.getElementById('todoColumn').innerHTML = '';
    document.getElementById('inProgressColumn').innerHTML = '';
    document.getElementById('completedColumn').innerHTML = '';
    
    // タスクを分類して表示
    const tasksByStatus = {
        todo: filteredTasks.filter(task => task.status === 'todo'),
        in_progress: filteredTasks.filter(task => task.status === 'in_progress'),
        completed: filteredTasks.filter(task => task.status === 'completed')
    };
    
    // 各カラムに表示
    Object.keys(tasksByStatus).forEach(status => {
        const column = document.getElementById(status === 'in_progress' ? 'inProgressColumn' : status + 'Column');
        const tasks = tasksByStatus[status];
        
        if (tasks.length === 0) {
            column.innerHTML = '<p class="text-gray-500 text-center py-8">タスクがありません</p>';
        } else {
            tasks.forEach(task => {
                column.appendChild(createTaskCard(task));
            });
        }
    });
    
    // カウント更新
    updateTaskCounts(tasksByStatus);
    
    // ドラッグ&ドロップ設定
    setupDragAndDrop();
}

function createTaskCard(task) {
    const card = document.createElement('div');
    card.className = `task-card bg-gray-50 rounded-lg p-4 border priority-${task.priority} hover:shadow-md transition-shadow fade-in`;
    card.draggable = true;
    card.dataset.taskId = task.id;
    
    const isOverdue = task.dueDate && new Date(task.dueDate) < new Date() && task.status !== 'completed';
    const priorityColors = {
        high: 'bg-red-100 text-red-800',
        medium: 'bg-yellow-100 text-yellow-800',
        low: 'bg-green-100 text-green-800'
    };
    
    const priorityLabels = {
        high: '高',
        medium: '中',
        low: '低'
    };
    
    card.innerHTML = `
        <div class="flex justify-between items-start mb-2">
            <h4 class="font-medium text-gray-900 flex-1 cursor-pointer hover:text-blue-600" onclick="openTaskModal(${task.id})">
                ${task.title}
            </h4>
            <span class="ml-2 px-2 py-1 text-xs rounded-full ${priorityColors[task.priority]}">
                ${priorityLabels[task.priority]}
            </span>
        </div>
        
        ${task.description ? `<p class="text-sm text-gray-600 mb-3">${task.description.substring(0, 100)}${task.description.length > 100 ? '...' : ''}</p>` : ''}
        
        ${task.dueDate ? `
            <div class="flex items-center mb-3 text-sm">
                <i class="fas fa-calendar mr-1 ${isOverdue ? 'text-red-500' : 'text-gray-400'}"></i>
                <span class="${isOverdue ? 'text-red-600 font-medium' : 'text-gray-600'}">
                    ${formatDate(task.dueDate)}
                    ${isOverdue ? ' (期限切れ)' : ''}
                </span>
            </div>
        ` : ''}
        
        <div class="flex justify-between items-center">
            <div class="flex space-x-1">
                ${task.status !== 'todo' ? `
                    <button onclick="changeTaskStatus(${task.id}, 'todo')" 
                            class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200 transition">
                        <i class="fas fa-undo"></i>
                    </button>
                ` : ''}
                
                ${task.status !== 'in_progress' ? `
                    <button onclick="changeTaskStatus(${task.id}, 'in_progress')" 
                            class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 transition">
                        <i class="fas fa-play"></i>
                    </button>
                ` : ''}
                
                ${task.status !== 'completed' ? `
                    <button onclick="changeTaskStatus(${task.id}, 'completed')" 
                            class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200 transition">
                        <i class="fas fa-check"></i>
                    </button>
                ` : ''}
            </div>
            
            <div class="flex space-x-1">
                <button onclick="openTaskModal(${task.id})" 
                        class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200 transition">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteTask(${task.id})" 
                        class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="mt-2 text-xs text-gray-400">
            作成: ${formatDate(task.createdAt, true)}
        </div>
    `;
    
    return card;
}

function updateTaskCounts(tasksByStatus) {
    document.getElementById('todoCount').textContent = tasksByStatus.todo.length;
    document.getElementById('inProgressCount').textContent = tasksByStatus.in_progress.length;
    document.getElementById('completedCount').textContent = tasksByStatus.completed.length;
}

// ===== 統計更新 =====
function updateStatistics() {
    const total = tasks.length;
    const completed = tasks.filter(task => task.status === 'completed').length;
    const inProgress = tasks.filter(task => task.status === 'in_progress').length;
    const completionRate = total > 0 ? Math.round((completed / total) * 100) : 0;
    
    document.getElementById('totalTasks').textContent = total;
    document.getElementById('completedTasks').textContent = completed;
    document.getElementById('inProgressTasks').textContent = inProgress;
    document.getElementById('completionRate').textContent = `${completionRate}%`;
    
    // チャート更新
    updateChart();
}

// ===== チャート関連 =====
function initializeChart() {
    const ctx = document.getElementById('taskChart').getContext('2d');
    taskChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['やること', '進行中', '完了'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: [
                    '#9CA3AF',
                    '#3B82F6',
                    '#10B981'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function updateChart() {
    if (!taskChart) return;
    
    const todo = tasks.filter(task => task.status === 'todo').length;
    const inProgress = tasks.filter(task => task.status === 'in_progress').length;
    const completed = tasks.filter(task => task.status === 'completed').length;
    
    taskChart.data.datasets[0].data = [todo, inProgress, completed];
    taskChart.update();
}

// ===== タスク操作関連 =====
function openTaskModal(taskId = null) {
    currentTaskId = taskId;
    const modal = document.getElementById('taskModal');
    const form = document.getElementById('taskForm');
    const modalTitle = document.getElementById('modalTitle');
    
    if (taskId) {
        // 編集モード
        const task = tasks.find(t => t.id === taskId);
        modalTitle.textContent = 'タスクを編集';
        
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskDescription').value = task.description || '';
        document.getElementById('taskPriority').value = task.priority;
        document.getElementById('taskStatus').value = task.status;
        document.getElementById('taskDueDate').value = task.dueDate || '';
    } else {
        // 新規作成モード
        modalTitle.textContent = 'タスクを追加';
        form.reset();
        document.getElementById('taskPriority').value = 'medium';
        document.getElementById('taskStatus').value = 'todo';
    }
    
    modal.classList.remove('hidden');
    document.getElementById('taskTitle').focus();
}

function closeTaskModal() {
    document.getElementById('taskModal').classList.add('hidden');
    currentTaskId = null;
}

function saveTask(event) {
    event.preventDefault();
    
    const title = document.getElementById('taskTitle').value.trim();
    const description = document.getElementById('taskDescription').value.trim();
    const priority = document.getElementById('taskPriority').value;
    const status = document.getElementById('taskStatus').value;
    const dueDate = document.getElementById('taskDueDate').value;
    
    // バリデーション
    if (!title) {
        showNotification('タスク名を入力してください', 'error');
        return;
    }
    
    if (currentTaskId) {
        // 更新
        const taskIndex = tasks.findIndex(t => t.id === currentTaskId);
        tasks[taskIndex] = {
            ...tasks[taskIndex],
            title,
            description,
            priority,
            status,
            dueDate: dueDate || null,
            updatedAt: new Date()
        };
        showNotification('タスクを更新しました', 'success');
    } else {
        // 新規作成
        const newTask = {
            id: Date.now(),
            title,
            description,
            priority,
            status,
            dueDate: dueDate || null,
            createdAt: new Date()
        };
        tasks.push(newTask);
        showNotification('タスクを作成しました', 'success');
    }
    
    // 画面更新
    displayTasks();
    updateStatistics();
    closeTaskModal();
    saveToLocalStorage();
}

function changeTaskStatus(taskId, newStatus) {
    const task = tasks.find(t => t.id === taskId);
    if (task) {
        task.status = newStatus;
        task.updatedAt = new Date();
        
        displayTasks();
        updateStatistics();
        saveToLocalStorage();
        
        const statusLabels = {
            todo: 'やること',
            in_progress: '進行中',
            completed: '完了'
        };
        showNotification(`ステータスを「${statusLabels[newStatus]}」に変更しました`, 'success');
    }
}

function deleteTask(taskId) {
    if (confirm('このタスクを削除しますか？')) {
        tasks = tasks.filter(t => t.id !== taskId);
        displayTasks();
        updateStatistics();
        saveToLocalStorage();
        showNotification('タスクを削除しました', 'success');
    }
}

// ===== フィルター機能 =====
function getFilteredTasks() {
    const priorityFilter = document.getElementById('priorityFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    return tasks.filter(task => {
        const matchesPriority = !priorityFilter || task.priority === priorityFilter;
        const matchesStatus = !statusFilter || task.status === statusFilter;
        const matchesSearch = !searchTerm || 
            task.title.toLowerCase().includes(searchTerm) ||
            (task.description && task.description.toLowerCase().includes(searchTerm));
        
        return matchesPriority && matchesStatus && matchesSearch;
    });
}

function applyFilters() {
    displayTasks();
}

function clearFilters() {
    document.getElementById('priorityFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('searchInput').value = '';
    displayTasks();
}

// ===== ドラッグ&ドロップ =====
function setupDragAndDrop() {
    const taskCards = document.querySelectorAll('.task-card');
    const dropZones = document.querySelectorAll('.drop-zone');
    
    taskCards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
    });
    
    dropZones.forEach(zone => {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('drop', handleDrop);
        zone.addEventListener('dragenter', handleDragEnter);
        zone.addEventListener('dragleave', handleDragLeave);
    });
}

function handleDragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.taskId);
    e.target.style.opacity = '0.5';
}

function handleDragEnd(e) {
    e.target.style.opacity = '';
}

function handleDragOver(e) {
    e.preventDefault();
}

function handleDragEnter(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function handleDragLeave(e) {
    e.currentTarget.classList.remove('drag-over');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    
    const taskId = parseInt(e.dataTransfer.getData('text/plain'));
    const newStatus = e.currentTarget.dataset.status;
    
    changeTaskStatus(taskId, newStatus);
}

// ===== ユーティリティ関数 =====
function formatDate(date, includeTime = false) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    
    if (includeTime) {
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${year}/${month}/${day} ${hours}:${minutes}`;
    }
    
    return `${year}/${month}/${day}`;
}

function showNotification(message, type = 'info') {
    const container = document.getElementById('notificationContainer');
    const notification = document.createElement('div');
    
    const typeColors = {
        success: 'bg-green-100 border-green-500 text-green-700',
        error: 'bg-red-100 border-red-500 text-red-700',
        info: 'bg-blue-100 border-blue-500 text-blue-700',
        warning: 'bg-yellow-100 border-yellow-500 text-yellow-700'
    };
    
    const typeIcons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle',
        warning: 'fa-exclamation-triangle'
    };
    
    notification.className = `${typeColors[type]} border-l-4 p-4 rounded shadow-lg slide-up mb-4`;
    notification.innerHTML = `
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas ${typeIcons[type]}"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                        class="inline-flex text-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(notification);
    
    // 5秒後に自動削除
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// ===== その他の機能 =====
function toggleDarkMode() {
    isDarkMode = !isDarkMode;
    document.body.classList.toggle('dark-mode');
    
    const icon = document.querySelector('#darkModeToggle i');
    icon.className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
    
    localStorage.setItem('darkMode', isDarkMode);
}

function exportTasks() {
    const dataStr = JSON.stringify(tasks, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    
    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = `tasks_${formatDate(new Date()).replace(/\//g, '-')}.json`;
    link.click();
    
    showNotification('タスクをエクスポートしました', 'success');
}

// ===== ローカルストレージ =====
function saveToLocalStorage() {
    localStorage.setItem('tasks', JSON.stringify(tasks));
}

function loadFromLocalStorage() {
    const savedTasks = localStorage.getItem('tasks');
    const savedDarkMode = localStorage.getItem('darkMode');
    
    if (savedTasks) {
        tasks = JSON.parse(savedTasks);
        displayTasks();
        updateStatistics();
    }
    
    if (savedDarkMode === 'true') {
        toggleDarkMode();
    }
}

// ===== Ajax通信のデモ（実際のAPIがある場合） =====
async function syncWithServer() {
    try {
        // GET request example
        const response = await fetch('/api/tasks');
        if (response.ok) {
            const serverTasks = await response.json();
            tasks = serverTasks;
            displayTasks();
            updateStatistics();
            showNotification('サーバーと同期しました', 'success');
        }
    } catch (error) {
        console.error('Sync error:', error);
        showNotification('同期に失敗しました', 'error');
    }
}

async function saveTaskToServer(task) {
    try {
        const response = await fetch('/api/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(task)
        });
        
        if (response.ok) {
            const savedTask = await response.json();
            return savedTask;
        }
    } catch (error) {
        console.error('Save error:', error);
        throw error;
    }
}