<?php
class ForumPost {
    private $conn;
    private $table_name = "forum_posts";

    public $id;
    public $user_id;
    public $title;
    public $content;
    public $created_at;
    public $updated_at;
    public $is_deleted;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Membaca semua post forum dengan pagination
    public function readAll($limit = 10, $offset = 0) {
        $query = "SELECT 
                    fp.id, fp.user_id, fp.title, fp.content, fp.created_at, fp.updated_at,
                    u.name as user_name,
                    (SELECT COUNT(*) FROM forum_replies fr WHERE fr.post_id = fp.id AND fr.is_deleted = 0) as reply_count
                  FROM " . $this->table_name . " fp
                  LEFT JOIN users u ON fp.user_id = u.id
                  WHERE fp.is_deleted = 0
                  ORDER BY fp.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    // Menghitung total post
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE is_deleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Membaca post berdasarkan ID
    public function readOne() {
        $query = "SELECT 
                    fp.id, fp.user_id, fp.title, fp.content, fp.created_at, fp.updated_at,
                    u.name as user_name,
                    u.alamat_email as user_email
                  FROM " . $this->table_name . " fp
                  LEFT JOIN users u ON fp.user_id = u.id
                  WHERE fp.id = :id AND fp.is_deleted = 0
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->user_id = $row['user_id'];
            $this->title = $row['title'];
            $this->content = $row['content'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return $row;
        }
        return false;
    }

    // Membuat post baru
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id = :user_id,
                    title = :title,
                    content = :content";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));

        // Bind values
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Update post
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    title = :title,
                    content = :content,
                    updated_at = CURRENT_TIMESTAMP
                WHERE
                    id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));

        // Bind values
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Soft delete post
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

    // Mencari post berdasarkan keyword
    public function search($keyword, $limit = 10, $offset = 0) {
        $query = "SELECT 
                    fp.id, fp.user_id, fp.title, fp.content, fp.created_at, fp.updated_at,
                    u.name as user_name,
                    (SELECT COUNT(*) FROM forum_replies fr WHERE fr.post_id = fp.id AND fr.is_deleted = 0) as reply_count
                  FROM " . $this->table_name . " fp
                  LEFT JOIN users u ON fp.user_id = u.id
                  WHERE fp.is_deleted = 0 
                  AND (fp.title LIKE :keyword OR fp.content LIKE :keyword)
                  ORDER BY fp.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    // Mendapatkan post milik user tertentu
    public function getUserPosts($user_id, $limit = 10, $offset = 0) {
        $query = "SELECT 
                    fp.id, fp.user_id, fp.title, fp.content, fp.created_at, fp.updated_at,
                    u.name as user_name,
                    (SELECT COUNT(*) FROM forum_replies fr WHERE fr.post_id = fp.id AND fr.is_deleted = 0) as reply_count
                  FROM " . $this->table_name . " fp
                  LEFT JOIN users u ON fp.user_id = u.id
                  WHERE fp.is_deleted = 0 AND fp.user_id = :user_id
                  ORDER BY fp.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }
}