<?php
class ForumReply {
    private $conn;
    private $table_name = "forum_replies";

    public $id;
    public $post_id;
    public $user_id;
    public $content;
    public $created_at;
    public $updated_at;
    public $is_deleted;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Membaca semua reply berdasarkan post_id
    public function getByPostId($post_id) {
        $query = "SELECT 
                    fr.id, fr.post_id, fr.user_id, fr.content, fr.created_at, fr.updated_at,
                    u.name as user_name,
                    u.alamat_email as user_email
                  FROM " . $this->table_name . " fr
                  LEFT JOIN users u ON fr.user_id = u.id
                  WHERE fr.post_id = :post_id AND fr.is_deleted = 0
                  ORDER BY fr.created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        return $stmt;
    }

    // Membaca reply berdasarkan ID
    public function readOne() {
        $query = "SELECT 
                    fr.id, fr.post_id, fr.user_id, fr.content, fr.created_at, fr.updated_at,
                    u.name as user_name,
                    u.alamat_email as user_email
                  FROM " . $this->table_name . " fr
                  LEFT JOIN users u ON fr.user_id = u.id
                  WHERE fr.id = :id AND fr.is_deleted = 0
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->post_id = $row['post_id'];
            $this->user_id = $row['user_id'];
            $this->content = $row['content'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return $row;
        }
        return false;
    }

    // Membuat reply baru
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    post_id = :post_id,
                    user_id = :user_id,
                    content = :content";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->post_id = htmlspecialchars(strip_tags($this->post_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->content = htmlspecialchars(strip_tags($this->content));

        // Bind values
        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':content', $this->content);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Update reply
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    content = :content,
                    updated_at = CURRENT_TIMESTAMP
                WHERE
                    id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->content = htmlspecialchars(strip_tags($this->content));

        // Bind values
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Soft delete reply
    public function delete() {
        $query = "UPDATE " . $this->table_name . "
                SET is_deleted = 1
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Menghitung jumlah reply berdasarkan post_id
    public function countByPostId($post_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE post_id = :post_id AND is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Mendapatkan reply milik user tertentu
    public function getUserReplies($user_id, $limit = 10, $offset = 0) {
        $query = "SELECT 
                    fr.id, fr.post_id, fr.user_id, fr.content, fr.created_at, fr.updated_at,
                    u.name as user_name,
                    fp.title as post_title
                  FROM " . $this->table_name . " fr
                  LEFT JOIN users u ON fr.user_id = u.id
                  LEFT JOIN forum_posts fp ON fr.post_id = fp.id
                  WHERE fr.is_deleted = 0 AND fr.user_id = :user_id
                  ORDER BY fr.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }
}