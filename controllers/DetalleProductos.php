<?php
require_once(__DIR__ . '/../models/DetalleProductos.php');

class DetallesProductosController {
    private $model;

    public function __construct($db) {
        $this->model = new Producto($db);
    }

    // Obtener productos por categoría
    public function obtenerPorCategoria($categoria_id = null) {
        return $this->model->getProductosPorCategoria($categoria_id);
    }

    // Obtener producto por ID
    public function obtenerPorId($id) {
        return $this->model->getProductoPorId($id);
    }
}
