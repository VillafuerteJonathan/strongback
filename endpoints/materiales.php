<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-HTTP-Method-Override");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/Material.php';

$database = new Database();
$db = $database->getConnection();
$controller = new MaterialesController($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['producto_id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Falta el parámetro producto_id']);
        exit;
    }
    $producto_id = intval($_GET['producto_id']);
    $materiales = $controller->obtener($producto_id);
    if ($materiales) {
        echo json_encode($materiales);
    } else {
        echo json_encode(null);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (
        !isset($input['producto_id'], $input['material'], $input['suela'], $input['forro'], $input['puntera'], $input['plantilla'])
    ) {
        http_response_code(400);
        echo json_encode(['message' => 'Faltan datos obligatorios']);
        exit;
    }

    $producto_id = intval($input['producto_id']);
    $data = [
        'material' => $input['material'],
        'suela' => $input['suela'],
        'forro' => $input['forro'],
        'puntera' => $input['puntera'],
        'plantilla' => $input['plantilla'],
    ];

    $actualizado = $controller->actualizar($producto_id, $data);

    if ($actualizado) {
        echo json_encode(['message' => 'Materiales actualizados correctamente']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error al actualizar materiales']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
