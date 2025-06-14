<?php
require_once 'config/database.php';
require_once 'models/TodoList.php';

class TodoListController {
    private $db;
    private $todo;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->todo = new TodoList($this->db);
    }
    
    // Show todo list page
    public function index() {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
};
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $current_page = 'todolist';
        $page_title = 'My Todo List - Campus Hub';
        
        // Get all todos for the user
        $this->todo->user_id = $_SESSION['user_id'];
        $stmt = $this->todo->readByUserId();
        $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get statistics
        $stats = $this->todo->getStats();
        
        include_once 'views/todolist/index.php';
    }
    
    // Create new todo
    public function create() {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
};
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->todo->user_id = $_SESSION['user_id'];
            $this->todo->title = $_POST['title'];
            $this->todo->description = $_POST['description'] ?? '';
            $this->todo->status = 'pending';
            $this->todo->priority = $_POST['priority'] ?? 'medium';
            $this->todo->due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
            
            if ($this->todo->create()) {
                header('Location: index.php?action=todolist&success=created');
            } else {
                header('Location: index.php?action=todolist&error=create_failed');
            }
        }
    }
    
    // Update todo
    public function update() {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
};
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $this->todo->id = $_POST['id'];
            $this->todo->user_id = $_SESSION['user_id'];
            $this->todo->title = $_POST['title'];
            $this->todo->description = $_POST['description'] ?? '';
            $this->todo->status = $_POST['status'];
            $this->todo->priority = $_POST['priority'];
            $this->todo->due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
            
            if ($this->todo->update()) {
                header('Location: index.php?action=todolist&success=updated');
            } else {
                header('Location: index.php?action=todolist&error=update_failed');
            }
        }
    }
    
    // Update status only (AJAX)
    public function updateStatus() {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
};
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $this->todo->id = $input['id'];
            $this->todo->user_id = $_SESSION['user_id'];
            $this->todo->status = $input['status'];
            
            if ($this->todo->updateStatus()) {
                echo json_encode(['success' => true, 'message' => 'Status updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Update failed']);
            }
        }
    }
    
    // Delete todo
    public function delete() {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
};
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        if (isset($_POST['id'])) {
            $this->todo->id = $_POST['id'];
            $this->todo->user_id = $_SESSION['user_id'];
            
            if ($this->todo->delete()) {
                header('Location: index.php?action=todolist&success=deleted');
            } else {
                header('Location: index.php?action=todolist&error=delete_failed');
            }
        }
    }
    
    // Get single todo (AJAX)
    public function getTodo() {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
};
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }
        
        if (isset($_GET['id'])) {
            $this->todo->id = $_GET['id'];
            $this->todo->user_id = $_SESSION['user_id'];
            
            if ($this->todo->readOne()) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'id' => $this->todo->id,
                        'title' => $this->todo->title,
                        'description' => $this->todo->description,
                        'status' => $this->todo->status,
                        'priority' => $this->todo->priority,
                        'due_date' => $this->todo->due_date
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Todo not found']);
            }
        }
    }
}