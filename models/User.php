<?php
require_once 'config/database.php';

class User {
    // Koneksi database dan nama tabel
    private $conn;
    private $table_name = "users";

    // Properti objek User
    public $id;
    public $name;
    public $alamat_email;
    public $password;
    public $no_telepon;
    public $created_at;

    // Konstruktor dengan koneksi database
    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE: Mendaftarkan user baru
    public function create() {
        // Query untuk insert
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name = :name,
                    alamat_email = :alamat_email,
                    password = :password,
                    no_telepon = :no_telepon,
                    created_at = :created_at";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->alamat_email = htmlspecialchars(strip_tags($this->alamat_email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->no_telepon = htmlspecialchars(strip_tags($this->no_telepon));
        $this->created_at = date('Y-m-d H:i:s');

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":alamat_email", $this->alamat_email);
        $stmt->bindParam(":no_telepon", $this->no_telepon);

        // Hash password sebelum disimpan
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":created_at", $this->created_at);

        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // READ: Mencari user berdasarkan email untuk login
    public function findByEmail() {
        // Query untuk mencari email
        $query = "SELECT id, name, alamat_email, password, no_telepon 
                FROM " . $this->table_name . " 
                WHERE alamat_email = ? 
                LIMIT 0,1";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind param
        $stmt->bindParam(1, $this->alamat_email);

        // Execute query
        $stmt->execute();

        // Mendapatkan jumlah baris
        $num = $stmt->rowCount();

        // Jika email ditemukan
        if($num > 0) {
            // Ambil record
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set nilai properti objek
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->alamat_email = $row['alamat_email'];
            $this->password = $row['password'];
            $this->no_telepon = $row['no_telepon'];

            return true;
        }
        return false;
    }

    // CHECK: Cek apakah email sudah digunakan
    public function emailExists() {
        // Query untuk cek email
        $query = "SELECT id 
                FROM " . $this->table_name . " 
                WHERE alamat_email = ? 
                LIMIT 0,1";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Bind param
        $stmt->bindParam(1, $this->alamat_email);

        // Execute query
        $stmt->execute();

        // Mendapatkan jumlah baris
        $num = $stmt->rowCount();

        // Jika email ditemukan
        if($num > 0) {
            return true;
        }
        return false;
    }

    // UPDATE: Memperbarui data user
    public function update() {
        // Query untuk update
        $query = "UPDATE " . $this->table_name . "
                SET
                    name = :name,
                    alamat_email = :alamat_email,
                    no_telepon = :no_telepon
                WHERE id = :id";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->alamat_email = htmlspecialchars(strip_tags($this->alamat_email));
        $this->no_telepon = htmlspecialchars(strip_tags($this->no_telepon));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":alamat_email", $this->alamat_email);
        $stmt->bindParam(":no_telepon", $this->no_telepon);
        $stmt->bindParam(":id", $this->id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // UPDATE: Memperbarui password
    public function updatePassword() {
        // Query untuk update password
        $query = "UPDATE " . $this->table_name . "
                SET password = :password
                WHERE id = :id";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Hash password baru
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        // Bind params
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id", $this->id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // DELETE: Menghapus akun user
    public function delete() {
        // Query untuk delete
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        // Prepare query
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind param
        $stmt->bindParam(1, $this->id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>