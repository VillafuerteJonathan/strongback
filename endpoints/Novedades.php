<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/NovedadesController.php';

// Conexión DB
$database = new Database();
$db = $database->getConnection();

// Instanciar controlador
$controller = new NovedadesController($db);

// Leer parámetro opcional de límite
$limite = isset($_GET['limite']) ? intval($_GET['limite']) : 5;

// Obtener últimos productos
$productos = $controller->obtenerUltimos($limite);

if ($productos) {
    echo json_encode($productos);
} else {
    echo json_encode(["message" => "No se encontraron productos"]);
}
