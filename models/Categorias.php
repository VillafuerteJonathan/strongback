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

    // Obtiene todas las categorías activas
    public function obtenerTodas() {
        $query = "SELECT * FROM {$this->table} WHERE activo = TRUE ORDER BY creado_en DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene categoría por ID, filtra por activo por defecto
    public function obtenerPorId($filtrarActivo = true) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        if ($filtrarActivo) {
            $query .= " AND activo = TRUE";
        }
        $query .= " LIMIT 1";

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

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function actualizar() {
        $query = "UPDATE {$this->table} SET nombre = :nombre, descripcion = :descripcion, imagen_url = :imagen_url WHERE id = :id AND activo = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':imagen_url', $this->imagen_url);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    // Corregido: usar $this->conn en vez de $this->db
    public function inactivarProductosPorCategoria($categoria_id) {
        try {
            $sql = "UPDATE productos 
                    SET estado = 0 
                    WHERE categoria_id = :categoria_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':categoria_id' => $categoria_id
            ]);
        } catch (Exception $e) {
            error_log("Error al inactivar productos por categoría: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar() {
        try {
            $this->conn->beginTransaction();

            // Inactivar la categoría
            $query = "UPDATE {$this->table} SET activo = 0 WHERE id = :id AND activo = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                $this->conn->rollBack();
                return false;
            }

            // Inactivar productos de la categoría
            if (!$this->inactivarProductosPorCategoria($this->id)) {
                $this->conn->rollBack();
                return false;
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar categoría y productos: " . $e->getMessage());
            return false;
        }
    }
}
