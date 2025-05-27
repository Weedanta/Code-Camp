<?php
class CV {
    private $conn;
    private $table_name = "cv_data";
    
    public $id;
    public $user_id;
    public $personal_info;
    public $experience;
    public $education;
    public $skills;
    public $projects;
    public $certifications;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create CV
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET user_id=:user_id, personal_info=:personal_info, experience=:experience, 
                     education=:education, skills=:skills, projects=:projects, 
                     certifications=:certifications, created_at=NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->personal_info = htmlspecialchars(strip_tags($this->personal_info));
        $this->experience = htmlspecialchars(strip_tags($this->experience));
        $this->education = htmlspecialchars(strip_tags($this->education));
        $this->skills = htmlspecialchars(strip_tags($this->skills));
        $this->projects = htmlspecialchars(strip_tags($this->projects));
        $this->certifications = htmlspecialchars(strip_tags($this->certifications));
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":personal_info", $this->personal_info);
        $stmt->bindParam(":experience", $this->experience);
        $stmt->bindParam(":education", $this->education);
        $stmt->bindParam(":skills", $this->skills);
        $stmt->bindParam(":projects", $this->projects);
        $stmt->bindParam(":certifications", $this->certifications);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Read CV by user ID
    public function readByUserId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        return $stmt;
    }
    
    // Update CV
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET personal_info=:personal_info, experience=:experience, education=:education, 
                     skills=:skills, projects=:projects, certifications=:certifications, 
                     updated_at=NOW() 
                 WHERE user_id=:user_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->personal_info = htmlspecialchars(strip_tags($this->personal_info));
        $this->experience = htmlspecialchars(strip_tags($this->experience));
        $this->education = htmlspecialchars(strip_tags($this->education));
        $this->skills = htmlspecialchars(strip_tags($this->skills));
        $this->projects = htmlspecialchars(strip_tags($this->projects));
        $this->certifications = htmlspecialchars(strip_tags($this->certifications));
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":personal_info", $this->personal_info);
        $stmt->bindParam(":experience", $this->experience);
        $stmt->bindParam(":education", $this->education);
        $stmt->bindParam(":skills", $this->skills);
        $stmt->bindParam(":projects", $this->projects);
        $stmt->bindParam(":certifications", $this->certifications);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Check if CV exists for user
    public function exists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}