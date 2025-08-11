<?php
class ImagenesProducto {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Obtener todas las imágenes de un producto
    public function obtenerPorProducto($productoId) {
        $sql = "SELECT id, url, angulo, orden FROM imagenes_producto WHERE producto_id = :producto_id ORDER BY orden ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':producto_id' => $productoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insertar nueva imagen para un producto
    public function insertar($productoId, $url, $angulo, $orden = 0) {
        $sql = "INSERT INTO imagenes_producto (producto_id, url, angulo, orden) VALUES (:producto_id, :url, :angulo, :orden)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':producto_id' => $productoId,
            ':url' => $url,
            ':angulo' => $angulo,
            ':orden' => $orden,
        ]);
    }

    // Actualizar imagen (por ejemplo, cambiar URL, angulo o orden)
    public function actualizar($id, $url, $angulo, $orden) {
        $sql = "UPDATE imagenes_producto SET url = :url, angulo = :angulo, orden = :orden WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':url' => $url,
            ':angulo' => $angulo,
            ':orden' => $orden,
            ':id' => $id
        ]);
    }

    // Eliminar imagen por id
    public function eliminar($id) {
        $sql = "DELETE FROM imagenes_producto WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
?>
