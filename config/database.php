<?php
class Database {
    private $host = "localhost";
    private $db_name = "strongecommerce";
    private $username = "root";      // Cambia si usas otro usuario
    private $password = "";          // Cambia si tienes contraseña
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $e) {
            error_log("Error de conexión BD: " . $e->getMessage());
            echo json_encode(["error" => "Error interno en el servidor"]);
            exit;
        }
        return $this->conn;
    }
}
