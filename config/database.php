<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'dashboard_eventos';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec('SET NAMES utf8');
        } catch(PDOException $e) {
            throw $e;
        }

        return $this->conn;
    }
}

// Create database connection
$database = new Database();
$pdo = $database->connect();
?>