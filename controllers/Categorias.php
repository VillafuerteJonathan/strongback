<?php
require_once(__DIR__ .'/../models/Categorias.php');

class CategoriaController {
    private $model;

    public function __construct($db) {
        $this->model = new Categoria($db);
    }

    public function obtenerTodas() {
        return $this->model->obtenerTodas();
    }

    public function obtenerPorId($id) {
        $this->model->id = $id;
        return $this->model->obtenerPorId();
    }

    public function crear($data) {
        $this->model->nombre = $data['nombre'];
        $this->model->descripcion = $data['descripcion'];
        $this->model->imagen_url = $data['imagen_url'];
        return $this->model->crear();
    }

    public function actualizar($data) {
        $this->model->id = $data['id'];
        $this->model->nombre = $data['nombre'];
        $this->model->descripcion = $data['descripcion'];
        if (isset($data['imagen_url'])) {
            $this->model->imagen_url = $data['imagen_url'];
        }
        return $this->model->actualizar();
    }

    public function eliminar($id) {
        $this->model->id = $id;
        return $this->model->eliminar();
    }
}
