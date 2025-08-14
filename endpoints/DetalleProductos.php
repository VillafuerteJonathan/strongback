<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';
require_once '../controllers/DetalleProductos.php';

// Conexión DB
$database = new Database();
$db = $database->getConnection();

// Instanciar controlador
$controller = new DetallesProductosController($db);

// Leer parámetros GET
$categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : null;
$producto_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($producto_id) {
    $producto = $controller->obtenerPorId($producto_id);
    if ($producto) {
        echo json_encode($producto);
    } else {
        echo json_encode(["message" => "Producto no encontrado"]);
    }
} else {
    $productos = $controller->obtenerPorCategoria($categoria_id);
    if ($productos) {
        echo json_encode($productos);
    } else {
        echo json_encode(["message" => "No se encontraron productos para esta categoría"]);
    }
}
