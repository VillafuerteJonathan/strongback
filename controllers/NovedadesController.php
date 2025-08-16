<?php
require_once __DIR__ . '/../models/Productos.php';

class NovedadesController {
    private $db;
    private $producto;

    public function __construct($db) {
        $this->db = $db;
        $this->producto = new Productos($db);
    }

    public function obtenerUltimos($limite = 5) {
        return $this->producto->getUltimosProductos($limite);
    }
}