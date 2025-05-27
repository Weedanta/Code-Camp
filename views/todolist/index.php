<?php
$base_url = '';
include_once $base_url . 'views/includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-4 md:py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- CV Builder Promotion Section -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-bold mb-2">Buat CV Profesional Dalam Hitungan Menit</h2>
                    <p class="text-blue-100 text-sm md:text-base">Tingkatkan peluang karier Anda dengan CV yang menarik dan ATS-friendly</p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <a href="index.php?action=cv_builder" class="bg-white text-blue-600 px-4 md:px-6 py-2 md:py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors text-center text-sm md:text-base">
                        <i class="fas fa-file-alt mr-2"></i>Buat CV Sekarang
                    </a>
                    <a href="index.php?action=cv_preview" class="bg-blue-500 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg font-semibold hover:bg-blue-400 transition-colors text-center text-sm md:text-base border border-blue-400">
                        <i class="fas fa-eye mr-2"></i>Lihat Preview
                    </a>
                </div>
            </div>
        </div>

        <!-- TodoList Header -->
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">My Todo List</h1>
                    <p class="text-gray-600 mt-2 text-sm md:text-base">Stay organized and track your progress</p>
                </div>
                <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors w-full sm:w-auto text-sm md:text-base">
                    <i class="fas fa-plus mr-2"></i>Add New Task
                </button>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <strong>Success!</strong> 
                <?php 
                switch($_GET['success']) {
                    case 'created': echo 'Task created successfully!'; break;
                    case 'updated': echo 'Task updated successfully!'; break;
                    case 'deleted': echo 'Task deleted successfully!'; break;
                    default: echo 'Action completed successfully!';
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>Error!</strong> There was a problem processing your request. Please try again.
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 md:p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-tasks text-lg md:text-xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Total Tasks</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900"><?php echo $stats['total'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 md:p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-lg md:text-xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Completed</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900"><?php echo $stats['completed'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 md:p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-lg md:text-xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">In Progress</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900"><?php echo $stats['in_progress'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 md:p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-exclamation-triangle text-lg md:text-xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">High Priority</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900"><?php echo $stats['high_priority_count'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex overflow-x-auto px-4 md:px-6 scrollbar-hide">
                    <button onclick="filterTodos('all')" class="filter-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-2 md:px-4 border-b-2 font-medium text-sm active" data-filter="all">
                        All Tasks
                    </button>
                    <button onclick="filterTodos('pending')" class="filter-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-2 md:px-4 border-b-2 font-medium text-sm" data-filter="pending">
                        Pending
                    </button>
                    <button onclick="filterTodos('in_progress')" class="filter-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-2 md:px-4 border-b-2 font-medium text-sm" data-filter="in_progress">
                        In Progress
                    </button>
                    <button onclick="filterTodos('completed')" class="filter-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-2 md:px-4 border-b-2 font-medium text-sm" data-filter="completed">
                        Completed
                    </button>
                </nav>
            </div>
        </div>

        <!-- Todo List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-4 md:p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Tasks</h2>
                <div id="todos-container">
                    <?php if (!empty($todos)): ?>
                        <?php foreach ($todos as $todo): ?>
                            <div class="todo-item border border-gray-200 rounded-lg p-3 md:p-4 mb-4 transition-all duration-200 hover:shadow-md" 
                                 data-status="<?php echo $todo['status']; ?>" data-priority="<?php echo $todo['priority']; ?>">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 flex-1 min-w-0">
                                        <!-- Status Checkbox -->
                                        <label class="flex items-center mt-1">
                                            <input type="checkbox" 
                                                   class="status-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                                   data-id="<?php echo $todo['id']; ?>"
                                                   <?php echo $todo['status'] == 'completed' ? 'checked' : ''; ?>>
                                        </label>
                                        
                                        <!-- Task Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2 mb-2">
                                                <h3 class="text-sm md:text-base font-medium text-gray-900 <?php echo $todo['status'] == 'completed' ? 'line-through text-gray-500' : ''; ?> break-words">
                                                    <?php echo htmlspecialchars($todo['title']); ?>
                                                </h3>
                                                
                                                <div class="flex flex-wrap gap-2 mt-1 sm:mt-0">
                                                    <!-- Priority Badge -->
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        <?php 
                                                        switch($todo['priority']) {
                                                            case 'high': echo 'bg-red-100 text-red-800'; break;
                                                            case 'medium': echo 'bg-yellow-100 text-yellow-800'; break;
                                                            case 'low': echo 'bg-green-100 text-green-800'; break;
                                                            default: echo 'bg-gray-100 text-gray-800';
                                                        }
                                                        ?>">
                                                        <?php echo ucfirst($todo['priority']); ?>
                                                    </span>
                                                    
                                                    <!-- Status Badge -->
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        <?php 
                                                        switch($todo['status']) {
                                                            case 'completed': echo 'bg-green-100 text-green-800'; break;
                                                            case 'in_progress': echo 'bg-blue-100 text-blue-800'; break;
                                                            case 'pending': echo 'bg-gray-100 text-gray-800'; break;
                                                            default: echo 'bg-gray-100 text-gray-800';
                                                        }
                                                        ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $todo['status'])); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <?php if (!empty($todo['description'])): ?>
                                                <p class="text-xs md:text-sm text-gray-600 mb-2 <?php echo $todo['status'] == 'completed' ? 'line-through' : ''; ?> break-words">
                                                    <?php echo htmlspecialchars($todo['description']); ?>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <div class="flex flex-col sm:flex-row sm:items-center text-xs text-gray-500 space-y-1 sm:space-y-0 sm:space-x-4">
                                                <?php if ($todo['due_date']): ?>
                                                    <span class="flex items-center">
                                                        <i class="fas fa-calendar-alt mr-1"></i>
                                                        Due: <?php echo date('M d, Y', strtotime($todo['due_date'])); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="flex items-center">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Created: <?php echo date('M d, Y', strtotime($todo['created_at'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-start space-x-1 ml-2">
                                        <button onclick="editTodo(<?php echo $todo['id']; ?>)" 
                                                class="text-blue-600 hover:text-blue-800 p-1 md:p-2" title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <form method="POST" action="index.php?action=todo_delete" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this task?')">
                                            <input type="hidden" name="id" value="<?php echo $todo['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-1 md:p-2" title="Delete">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <i class="fas fa-tasks text-4xl md:text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks yet</h3>
                            <p class="text-gray-600 mb-4 text-sm md:text-base">Get started by creating your first task!</p>
                            <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add Task
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Todo Modal -->
<div id="todoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-4 md:p-6 max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add New Task</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="todoForm" method="POST">
                <input type="hidden" id="todoId" name="id">
                
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Task Title</label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base">
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base"></textarea>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select id="priority" name="priority" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date (Optional)</label>
                    <input type="date" id="due_date" name="due_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base">
                </div>
                
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors text-sm md:text-base">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm md:text-base">
                        <i class="fas fa-save mr-2"></i>Save Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

/* Mobile responsive improvements */
@media (max-width: 640px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .todo-item {
        padding: 12px;
    }
    
    .todo-item h3 {
        font-size: 14px;
        line-height: 1.4;
    }
    
    .todo-item p {
        font-size: 12px;
        line-height: 1.3;
    }
    
    /* Stack badges vertically on very small screens */
    @media (max-width: 480px) {
        .todo-item .flex.flex-wrap.gap-2 {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .todo-item .inline-flex {
            width: fit-content;
        }
    }
}

/* Improve modal on mobile */
@media (max-width: 768px) {
    #todoModal .bg-white {
        margin: 1rem;
        max-height: calc(100vh - 2rem);
    }
}
</style>

<script>
// Modal functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Task';
    document.getElementById('todoForm').action = 'index.php?action=todo_create';
    document.getElementById('todoForm').reset();
    document.getElementById('todoId').value = '';
    document.getElementById('status').style.display = 'none';
    document.getElementById('status').parentElement.style.display = 'none';
    document.getElementById('todoModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scroll
}

function closeModal() {
    document.getElementById('todoModal').classList.add('hidden');
    document.body.style.overflow = 'auto'; // Re-enable background scroll
}

function editTodo(id) {
    fetch(`index.php?action=todo_get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalTitle').textContent = 'Edit Task';
                document.getElementById('todoForm').action = 'index.php?action=todo_update';
                document.getElementById('todoId').value = data.data.id;
                document.getElementById('title').value = data.data.title;
                document.getElementById('description').value = data.data.description;
                document.getElementById('priority').value = data.data.priority;
                document.getElementById('status').value = data.data.status;
                document.getElementById('due_date').value = data.data.due_date || '';
                document.getElementById('status').style.display = 'block';
                document.getElementById('status').parentElement.style.display = 'block';
                document.getElementById('todoModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scroll
            } else {
                alert('Error loading task data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading task data');
        });
}

// Status update via checkbox
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.status-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const todoId = this.dataset.id;
            const status = this.checked ? 'completed' : 'pending';
            
            fetch('index.php?action=todo_update_status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: todoId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh to update UI
                } else {
                    alert('Error updating task status');
                    this.checked = !this.checked; // Revert checkbox
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating task status');
                this.checked = !this.checked; // Revert checkbox
            });
        });
    });
});

// Filter functionality
function filterTodos(filter) {
    const todos = document.querySelectorAll('.todo-item');
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    // Update active filter button
    filterBtns.forEach(btn => {
        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    const activeBtn = document.querySelector(`[data-filter="${filter}"]`);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('active', 'border-blue-500', 'text-blue-600');
    
    // Filter todos
    todos.forEach(todo => {
        if (filter === 'all') {
            todo.style.display = 'block';
        } else {
            const todoStatus = todo.dataset.status;
            if (todoStatus === filter) {
                todo.style.display = 'block';
            } else {
                todo.style.display = 'none';
            }
        }
    });
}

// Close modal when clicking outside
document.getElementById('todoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Prevent modal scroll issues on mobile
document.getElementById('todoModal').addEventListener('touchmove', function(e) {
    e.preventDefault();
}, { passive: false });
</script>

<?php include_once $base_url . 'views/includes/footer.php'; ?>