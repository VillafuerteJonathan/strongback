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

    public function obtenerPorId($id, $filtrarActivo = true) {
        $this->model->id = $id;
        return $this->model->obtenerPorId($filtrarActivo);
    }

    public function crear($data) {
        $this->model->nombre = $data['nombre'];
        $this->model->descripcion = $data['descripcion'];
        $this->model->imagen_url = $data['imagen_url'];

        $resultado = $this->model->crear();

        // Devuelve el ID creado o false si falló
        if ($resultado) {
            return ['id' => $resultado];
        }
        return false;
    }

    public function actualizar($data) {
        $this->model->id = $data['id'];
        $this->model->nombre = $data['nombre'];
        $this->model->descripcion = $data['descripcion'];

        if (isset($data['imagen_url'])) {
            $this->model->imagen_url = $data['imagen_url'];
        } else {
            // Mantener imagen_url actual si no viene nueva imagen
            $categoriaActual = $this->obtenerPorId($data['id']);
            if ($categoriaActual && isset($categoriaActual['imagen_url'])) {
                $this->model->imagen_url = $categoriaActual['imagen_url'];
            } else {
                $this->model->imagen_url = null;
            }
        }

        return $this->model->actualizar();
    }

    public function eliminar($id) {
        $this->model->id = $id;
        $resultado = $this->model->eliminar();

        // Retorna true si eliminó, false si no
        return $resultado;
    }
}
