<?php
require_once __DIR__ . '/../models/Productos.php';

class ProductosController {
    private $model;

    public function __construct($db) {
        $this->model = new Productos($db);
    }

    public function crear($data, $imagenes) {
        return $this->model->crearProducto($data, $imagenes);
    }

    public function obtenerTodos() {
        return $this->model->obtenerProductosConTodo();
    }

    public function obtenerMateriales($productoId) {
        return $this->model->obtenerMaterialesPorProducto($productoId);
    }

    public function editar($id, $data) {
        return $this->model->editarProducto($id, $data);
    }
     public function eliminar($id) {
        return $this->model->eliminarProducto($id);
    }
}
