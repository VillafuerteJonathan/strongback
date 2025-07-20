<?php
require_once __DIR__ . '/../models/Material.php';

class MaterialesController {
    private $model;

    public function __construct($db) {
        $this->model = new Materiales($db);
    }

    public function obtener($producto_id) {
        return $this->model->obtenerPorProducto($producto_id);
    }

    public function actualizar($producto_id, $data) {
        return $this->model->actualizarPorProducto($producto_id, $data);
    }
}
