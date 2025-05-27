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
        
        // Check if data is corrupted (htmlspecialchars issue)
        if ($cv_data) {
            $test_personal = json_decode($cv_data['personal_info'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Data is corrupted, delete it
                $this->cv->delete();
                $cv_data = false;
                
                // Set error message
                header('Location: index.php?action=cv_builder&error=corrupted_data');
                exit();
            }
        }
        
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
            
            // Handle personal_info
            $personal_info = [];
            if (isset($_POST['personal_info'])) {
                $personal_info = $_POST['personal_info'];
            }
            $this->cv->personal_info = json_encode($personal_info, JSON_UNESCAPED_UNICODE);
            
            // Handle experience
            $experience = [];
            if (isset($_POST['experience']) && is_array($_POST['experience'])) {
                foreach ($_POST['experience'] as $exp) {
                    if (!empty($exp['title']) || !empty($exp['company'])) {
                        $experience[] = $exp;
                    }
                }
            }
            $this->cv->experience = json_encode($experience, JSON_UNESCAPED_UNICODE);
            
            // Handle education
            $education = [];
            if (isset($_POST['education']) && is_array($_POST['education'])) {
                foreach ($_POST['education'] as $edu) {
                    if (!empty($edu['degree']) || !empty($edu['institution'])) {
                        $education[] = $edu;
                    }
                }
            }
            $this->cv->education = json_encode($education, JSON_UNESCAPED_UNICODE);
            
            // Handle skills
            $skills = [];
            if (isset($_POST['skills'])) {
                $skills = $_POST['skills'];
            }
            $this->cv->skills = json_encode($skills, JSON_UNESCAPED_UNICODE);
            
            // Handle projects
            $projects = [];
            if (isset($_POST['projects']) && is_array($_POST['projects'])) {
                foreach ($_POST['projects'] as $project) {
                    if (!empty($project['name'])) {
                        $projects[] = $project;
                    }
                }
            }
            $this->cv->projects = json_encode($projects, JSON_UNESCAPED_UNICODE);
            
            // Handle certifications
            $certifications = [];
            if (isset($_POST['certifications']) && is_array($_POST['certifications'])) {
                foreach ($_POST['certifications'] as $cert) {
                    if (!empty($cert['name'])) {
                        $certifications[] = $cert;
                    }
                }
            }
            $this->cv->certifications = json_encode($certifications, JSON_UNESCAPED_UNICODE);
            
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
    
    // Delete CV data
    public function delete() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $this->cv->user_id = $_SESSION['user_id'];
        
        if ($this->cv->delete()) {
            header('Location: index.php?action=cv_builder&success=deleted');
        } else {
            header('Location: index.php?action=cv_builder&error=delete_failed');
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
        $personal_info = json_decode($cv_data['personal_info'], true) ?: [];
        $experience = json_decode($cv_data['experience'], true) ?: [];
        $education = json_decode($cv_data['education'], true) ?: [];
        $skills = json_decode($cv_data['skills'], true) ?: [];
        $projects = json_decode($cv_data['projects'], true) ?: [];
        $certifications = json_decode($cv_data['certifications'], true) ?: [];
        
        // Check if we have meaningful data
        $has_meaningful_data = (
            !empty($personal_info['full_name']) || 
            !empty($personal_info['email']) ||
            !empty($experience) || 
            !empty($education) || 
            !empty($skills['technical']) || 
            !empty($skills['soft']) ||
            !empty($projects) || 
            !empty($certifications)
        );
        
        if (!$has_meaningful_data) {
            header('Location: index.php?action=cv_builder&error=no_data');
            exit();
        }
        
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
        
        // Debug: Check if we have any CV data
        $has_cv_data = false;
        $personal_info = [];
        $experience = [];
        $education = [];
        $skills = [];
        $projects = [];
        $certifications = [];
        
        if ($cv_data) {
            // Parse JSON data with fallback to empty arrays
            $personal_info = json_decode($cv_data['personal_info'], true) ?: [];
            $experience = json_decode($cv_data['experience'], true) ?: [];
            $education = json_decode($cv_data['education'], true) ?: [];
            $skills = json_decode($cv_data['skills'], true) ?: [];
            $projects = json_decode($cv_data['projects'], true) ?: [];
            $certifications = json_decode($cv_data['certifications'], true) ?: [];
            
            // Check if we have any meaningful data
            $has_cv_data = (
                !empty($personal_info['full_name']) || 
                !empty($personal_info['email']) ||
                !empty($experience) || 
                !empty($education) || 
                !empty($skills['technical']) || 
                !empty($skills['soft']) ||
                !empty($projects) || 
                !empty($certifications)
            );
        }
        
        $current_page = 'cv_preview';
        $page_title = 'CV Preview - Campus Hub';
        
        include_once 'views/cv/preview.php';
    }
    
    // Get CV data as JSON (for AJAX)
    public function getData() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }
        
        $this->cv->user_id = $_SESSION['user_id'];
        $stmt = $this->cv->readByUserId();
        $cv_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cv_data) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'personal_info' => json_decode($cv_data['personal_info'], true) ?: [],
                    'experience' => json_decode($cv_data['experience'], true) ?: [],
                    'education' => json_decode($cv_data['education'], true) ?: [],
                    'skills' => json_decode($cv_data['skills'], true) ?: [],
                    'projects' => json_decode($cv_data['projects'], true) ?: [],
                    'certifications' => json_decode($cv_data['certifications'], true) ?: []
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No CV data found']);
        }
    }
    
    // Debug method - temporary for troubleshooting
    public function debug() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo "Not authenticated";
            exit();
        }
        
        $this->cv->user_id = $_SESSION['user_id'];
        $stmt = $this->cv->readByUserId();
        $cv_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>Debug CV Data:</h3>";
        echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
        
        if ($cv_data) {
            echo "<p>CV Data Found!</p>";
            echo "<pre>";
            print_r($cv_data);
            echo "</pre>";
            
            echo "<h4>JSON Validation:</h4>";
            $personal_info = json_decode($cv_data['personal_info'], true);
            $json_error = json_last_error();
            
            echo "<p>JSON Error Code: " . $json_error . "</p>";
            echo "<p>JSON Error Message: " . json_last_error_msg() . "</p>";
            
            if ($json_error !== JSON_ERROR_NONE) {
                echo "<p style='color: red;'>JSON DATA IS CORRUPTED!</p>";
                echo "<p>Raw personal_info data:</p>";
                echo "<pre>" . htmlspecialchars($cv_data['personal_info']) . "</pre>";
                
                echo "<p><a href='index.php?action=cv_cleanup' style='background: red; color: white; padding: 10px; text-decoration: none;'>CLEAN UP CORRUPTED DATA</a></p>";
            } else {
                echo "<h4>Parsed Data:</h4>";
                echo "<pre>";
                $experience = json_decode($cv_data['experience'], true);
                $education = json_decode($cv_data['education'], true);
                $skills = json_decode($cv_data['skills'], true);
                $projects = json_decode($cv_data['projects'], true);
                $certifications = json_decode($cv_data['certifications'], true);
                
                echo "Personal Info: ";
                print_r($personal_info);
                echo "\nExperience: ";
                print_r($experience);
                echo "\nEducation: ";
                print_r($education);
                echo "\nSkills: ";
                print_r($skills);
                echo "\nProjects: ";
                print_r($projects);
                echo "\nCertifications: ";
                print_r($certifications);
                echo "</pre>";
            }
        } else {
            echo "<p>No CV Data Found!</p>";
            
            // Check if user exists in database
            $query = "SELECT id FROM users WHERE id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":user_id", $_SESSION['user_id']);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo "<p>User exists in database</p>";
            } else {
                echo "<p>User does NOT exist in database</p>";
            }
            
            // Check CV table structure
            $query = "SHOW TABLES LIKE 'cv_data'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo "<p>cv_data table exists</p>";
            } else {
                echo "<p>cv_data table does NOT exist</p>";
            }
        }
        
        echo "<p><a href='index.php?action=cv_builder'>Back to CV Builder</a></p>";
    }
    
    // Cleanup corrupted data
    public function cleanup() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }
        
        $this->cv->user_id = $_SESSION['user_id'];
        
        if ($this->cv->delete()) {
            header('Location: index.php?action=cv_builder&success=cleanup');
        } else {
            header('Location: index.php?action=cv_builder&error=cleanup_failed');
        }
    }
}