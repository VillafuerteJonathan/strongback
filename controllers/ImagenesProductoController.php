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

    public function editarImagenPorOrdenYAngulo($productoId, $orden, $anguloActual, $nuevoArchivo, $nuevoAngulo) {
        return $this->model->editarImagenPorOrdenYAngulo($productoId, $orden, $anguloActual, $nuevoArchivo, $nuevoAngulo);
    }
}
?>
