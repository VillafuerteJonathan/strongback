<?php
class Productos {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function crearProducto($data, $imagenes) {
        try {
            $this->db->beginTransaction();

            // Insertar producto
            $sql = "INSERT INTO productos (nombre, descripcion, precio, categoria_id, disponible, imagen_principal, estado) 
                    VALUES (:nombre, :descripcion, :precio, :categoria_id, :disponible, :imagen_principal, 1)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':descripcion' => $data['descripcion'],
                ':precio' => $data['precio'],
                ':categoria_id' => $data['categoria_id'],
                ':disponible' => $data['disponible'],
                ':imagen_principal' => $data['imagen_principal'],
            ]);
            $producto_id = $this->db->lastInsertId();

            // Insertar materiales
            $sqlMat = "INSERT INTO materiales (producto_id, material, suela, forro, puntera, plantilla) 
                       VALUES (:producto_id, :material, :suela, :forro, :puntera, :plantilla)";
            $stmtMat = $this->db->prepare($sqlMat);
            $stmtMat->execute([
                ':producto_id' => $producto_id,
                ':material' => $data['material'] ?? null,
                ':suela' => $data['suela'] ?? null,
                ':forro' => $data['forro'] ?? null,
                ':puntera' => $data['puntera'] ?? null,
                ':plantilla' => $data['plantilla'] ?? null,
            ]);

            // Insertar tallas
            $sqlTalla = "INSERT INTO producto_talla (producto_id, talla_desde, talla_hasta) 
                         VALUES (:producto_id, :talla_desde, :talla_hasta)";
            $stmtTalla = $this->db->prepare($sqlTalla);
            $stmtTalla->execute([
                ':producto_id' => $producto_id,
                ':talla_desde' => $data['talla_desde'],
                ':talla_hasta' => $data['talla_hasta'],
            ]);

            // Insertar imágenes adicionales
            if (!empty($imagenes)) {
                $sqlImg = "INSERT INTO imagenes_producto (producto_id, url, angulo, orden) 
                           VALUES (:producto_id, :url, :angulo, :orden)";
                $stmtImg = $this->db->prepare($sqlImg);
                foreach ($imagenes as $angulo => $url) {
                    $stmtImg->execute([
                        ':producto_id' => $producto_id,
                        ':url' => $url,
                        ':angulo' => $angulo,
                        ':orden' => 0,
                    ]);
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear producto: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerProductosConTodo() {
        $sql = "
            SELECT 
                p.*,
                t.talla_desde,
                t.talla_hasta,
                m.material,
                m.suela,
                m.forro,
                m.puntera,
                m.plantilla,
                GROUP_CONCAT(DISTINCT i.url) AS imagenes_adicionales
            FROM productos p
            LEFT JOIN producto_talla t ON t.producto_id = p.id
            LEFT JOIN materiales m ON m.producto_id = p.id
            LEFT JOIN imagenes_producto i ON i.producto_id = p.id
            WHERE p.estado = 1
            GROUP BY p.id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerMaterialesPorProducto($productoId) {
        $sql = "SELECT * FROM materiales WHERE producto_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function editarProducto($id, $data) {
        try {
            $sql = "UPDATE productos 
                    SET nombre = :nombre, descripcion = :descripcion, precio = :precio, categoria_id = :categoria_id, 
                        disponible = :disponible, imagen_principal = :imagen_principal 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nombre' => $data['nombre'],
                ':descripcion' => $data['descripcion'],
                ':precio' => $data['precio'],
                ':categoria_id' => $data['categoria_id'],
                ':disponible' => $data['disponible'],
                ':imagen_principal' => $data['imagen_principal'],
                ':id' => $id
            ]);
        } catch (Exception $e) {
            error_log("Error al editar producto: " . $e->getMessage());
            return false;
        }
    }
        public function eliminarProducto($id) {
            try {
                $sql = "UPDATE productos 
                        SET estado = 0 
                        WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([':id' => $id]);
            } catch (Exception $e) {
                error_log("Error al inactivar producto: " . $e->getMessage());
                return false;
            }
        }

}
