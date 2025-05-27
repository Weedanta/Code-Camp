<?php
// models/Admin.php

class Admin {
    private $conn;
    private $table = 'admin';
    private $users_table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login admin
    public function login($email, $password) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $admin['password'])) {
                    return $admin;
                }
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Get all users
    public function getAllUsers() {
        try {
            $query = "SELECT id, name, alamat_email, no_telepon, created_at, updated_at FROM " . $this->users_table . " ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Get user by ID
    public function getUserById($id) {
        try {
            $query = "SELECT * FROM " . $this->users_table . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Update user
    public function updateUser($id, $name, $email, $phone) {
        try {
            // Check if email already exists for other users
            $checkQuery = "SELECT id FROM " . $this->users_table . " WHERE alamat_email = :email AND id != :id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email sudah digunakan oleh user lain'];
            }

            $query = "UPDATE " . $this->users_table . " SET name = :name, alamat_email = :email, no_telepon = :phone, updated_at = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Data user berhasil diupdate'];
            }
            return ['success' => false, 'message' => 'Gagal mengupdate data user'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // Delete user
    public function deleteUser($id) {
        try {
            // Check if user exists
            $checkQuery = "SELECT id FROM " . $this->users_table . " WHERE id = :id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkStmt->execute();

            if ($checkStmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'User tidak ditemukan'];
            }

            $query = "DELETE FROM " . $this->users_table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'User berhasil dihapus'];
            }
            return ['success' => false, 'message' => 'Gagal menghapus user'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error database: ' . $e->getMessage()];
        }
    }

    // Log admin activity
    public function logActivity($admin_id, $activity_type, $description, $ip_address = null, $user_agent = null) {
        try {
            $query = "INSERT INTO admin_activity_log (admin_id, activity_type, description, ip_address, user_agent, created_at) 
                     VALUES (:admin_id, :activity_type, :description, :ip_address, :user_agent, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':admin_id', $admin_id);
            $stmt->bindParam(':activity_type', $activity_type);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->bindParam(':user_agent', $user_agent);
            $stmt->execute();
        } catch (PDOException $e) {
            // Log error silently
        }
    }

    // Get user count
    public function getUserCount() {
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->users_table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}