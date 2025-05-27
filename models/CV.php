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
        
        // Sanitize user_id only, NOT the JSON data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // JSON data should NOT be htmlspecialchars'd - it will break the JSON format
        // The data is already properly formatted as JSON from the controller
        
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
        
        // Sanitize user_id only, NOT the JSON data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // JSON data should NOT be htmlspecialchars'd - it will break the JSON format
        // The data is already properly formatted as JSON from the controller
        
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
    
    // Delete CV
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}