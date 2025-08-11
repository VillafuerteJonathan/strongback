<?php
require_once '../models/ImagenesProducto.php';

class ImagenesProductoController {
    private $model;

    public function __construct($db) {
        $this->model = new ImagenesProducto($db);
    }

    public function obtenerPorProducto($productoId) {
        return $this->model->obtenerPorProducto($productoId);
    }

    public function insertar($data) {
        if (!isset($data['producto_id'], $data['url'], $data['angulo'])) {
            return ['success' => false, 'message' => 'Faltan datos'];
        }

        $orden = $data['orden'] ?? 0;
        $resultado = $this->model->insertar($data['producto_id'], $data['url'], $data['angulo'], $orden);
        if ($resultado) {
            return ['success' => true, 'message' => 'Imagen insertada'];
        } else {
            return ['success' => false, 'message' => 'Error al insertar imagen'];
        }
    }

    public function actualizar($data) {
        if (!isset($data['id'], $data['url'], $data['angulo'], $data['orden'])) {
            return ['success' => false, 'message' => 'Faltan datos'];
        }

        $resultado = $this->model->actualizar($data['id'], $data['url'], $data['angulo'], $data['orden']);
        if ($resultado) {
            return ['success' => true, 'message' => 'Imagen actualizada'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar imagen'];
        }
    }

    public function eliminar($id) {
        $resultado = $this->model->eliminar($id);
        if ($resultado) {
            return ['success' => true, 'message' => 'Imagen eliminada'];
        } else {
            return ['success' => false, 'message' => 'Error al eliminar imagen'];
        }
    }
}
?>
