<?php
class Materiales {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Obtener materiales por producto_id (solo un registro de materiales por producto)
    public function obtenerPorProducto($producto_id) {
        $sql = "SELECT material, suela, forro, puntera, plantilla FROM materiales WHERE producto_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$producto_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar materiales de un producto (si no existe, insertarlo)
    public function actualizarPorProducto($producto_id, $data) {
        // Verificar si ya existen materiales para el producto
        $sqlCheck = "SELECT COUNT(*) FROM materiales WHERE producto_id = ?";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([$producto_id]);
        $existe = $stmtCheck->fetchColumn() > 0;

        if ($existe) {
            // Update
            $sqlUpdate = "UPDATE materiales SET 
                material = :material, 
                suela = :suela, 
                forro = :forro, 
                puntera = :puntera, 
                plantilla = :plantilla
                WHERE producto_id = :producto_id";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            return $stmtUpdate->execute([
                ':material' => $data['material'],
                ':suela' => $data['suela'],
                ':forro' => $data['forro'],
                ':puntera' => $data['puntera'],
                ':plantilla' => $data['plantilla'],
                ':producto_id' => $producto_id
            ]);
        } else {
            // Insert
            $sqlInsert = "INSERT INTO materiales (producto_id, material, suela, forro, puntera, plantilla)
                          VALUES (:producto_id, :material, :suela, :forro, :puntera, :plantilla)";
            $stmtInsert = $this->db->prepare($sqlInsert);
            return $stmtInsert->execute([
                ':producto_id' => $producto_id,
                ':material' => $data['material'],
                ':suela' => $data['suela'],
                ':forro' => $data['forro'],
                ':puntera' => $data['puntera'],
                ':plantilla' => $data['plantilla']
            ]);
        }
    }
}
