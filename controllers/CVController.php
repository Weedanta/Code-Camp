<?php
require_once 'config/database.php';
require_once 'models/CV.php';

class CVController {
    private $db;
    private $cv;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->cv = new CV($this->db);
    }
    
    // Show CV builder page
    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $current_page = 'cv_builder';
        $page_title = 'CV Builder - Campus Hub';
        
        // Get existing CV data
        $this->cv->user_id = $_SESSION['user_id'];
        $stmt = $this->cv->readByUserId();
        $cv_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        include_once 'views/cv/index.php';
    }
    
    // Save CV data
    public function save() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->cv->user_id = $_SESSION['user_id'];
            $this->cv->personal_info = json_encode($_POST['personal_info']);
            $this->cv->experience = json_encode($_POST['experience']);
            $this->cv->education = json_encode($_POST['education']);
            $this->cv->skills = json_encode($_POST['skills']);
            $this->cv->projects = json_encode($_POST['projects']);
            $this->cv->certifications = json_encode($_POST['certifications']);
            
            // Check if CV exists
            if ($this->cv->exists()) {
                if ($this->cv->update()) {
                    header('Location: index.php?action=cv_builder&success=updated');
                } else {
                    header('Location: index.php?action=cv_builder&error=update_failed');
                }
            } else {
                if ($this->cv->create()) {
                    header('Location: index.php?action=cv_builder&success=created');
                } else {
                    header('Location: index.php?action=cv_builder&error=create_failed');
                }
            }
        }
    }
    
    // Generate PDF CV
    public function generatePDF() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $this->cv->user_id = $_SESSION['user_id'];
        $stmt = $this->cv->readByUserId();
        $cv_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cv_data) {
            header('Location: index.php?action=cv_builder&error=no_data');
            exit();
        }
        
        // Parse JSON data
        $personal_info = json_decode($cv_data['personal_info'], true);
        $experience = json_decode($cv_data['experience'], true);
        $education = json_decode($cv_data['education'], true);
        $skills = json_decode($cv_data['skills'], true);
        $projects = json_decode($cv_data['projects'], true);
        $certifications = json_decode($cv_data['certifications'], true);
        
        include_once 'views/cv/pdf.php';
    }
    
    // Preview CV
    public function preview() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $this->cv->user_id = $_SESSION['user_id'];
        $stmt = $this->cv->readByUserId();
        $cv_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cv_data) {
            header('Location: index.php?action=cv_builder&error=no_data');
            exit();
        }
        
        // Parse JSON data
        $personal_info = json_decode($cv_data['personal_info'], true);
        $experience = json_decode($cv_data['experience'], true);
        $education = json_decode($cv_data['education'], true);
        $skills = json_decode($cv_data['skills'], true);
        $projects = json_decode($cv_data['projects'], true);
        $certifications = json_decode($cv_data['certifications'], true);
        
        $current_page = 'cv_preview';
        $page_title = 'CV Preview - Campus Hub';
        
        include_once 'views/cv/preview.php';
    }
}