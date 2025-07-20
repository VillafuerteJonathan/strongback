<?php
class Categoria {
    private $conn;
    private $table = "categorias";

    public $id;
    public $nombre;
    public $descripcion;
    public $imagen_url;
    public $activo = true;
    public $creado_en;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTodas() {
        $query = "SELECT * FROM {$this->table} WHERE activo = TRUE ORDER BY creado_en DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId() {
        $query = "SELECT * FROM {$this->table} WHERE id = :id AND activo = TRUE LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear() {
        $query = "INSERT INTO {$this->table} (nombre, descripcion, imagen_url) VALUES (:nombre, :descripcion, :imagen_url)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':imagen_url', $this->imagen_url);
        return $stmt->execute();
    }

    public function actualizar() {
        $query = "UPDATE {$this->table} SET nombre = :nombre, descripcion = :descripcion, imagen_url = :imagen_url WHERE id = :id AND activo = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':imagen_url', $this->imagen_url);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function eliminar() {
        $query = "UPDATE {$this->table} SET activo = FALSE WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}

