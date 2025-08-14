<?php
class Producto {
    private $conn;
    private $table = 'productos';

    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $imagen_url;
    public $categoria_id;

    public function __construct($db) {
        $this->conn = $db; // PDO connection
    }

    // Obtener productos por categoría o todos si no se pasa categoría
    public function getProductosPorCategoria($categoria_id = null) {
        if ($categoria_id) {
            $query = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.imagen_principal AS imagen_url, c.nombre AS categoria
                      FROM productos p
                      JOIN categorias c ON p.categoria_id = c.id
                      WHERE p.categoria_id = :categoria_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':categoria_id', $categoria_id, PDO::PARAM_INT);
        } else {
            $query = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.imagen_principal AS imagen_url, c.nombre AS categoria
                      FROM productos p
                      JOIN categorias c ON p.categoria_id = c.id";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un producto por ID
    public function getProductoPorId($id) {
        $query = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.imagen_principal AS imagen_url, c.nombre AS categoria
                  FROM productos p
                  JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
