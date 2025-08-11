<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php'; // Aquí va la clase Database para la conexión PDO
require_once '../controllers/ImagenesProductoController.php';

// Crear conexión a la base de datos
$database = new Database();
$pdo = $database->getConnection();

// Crear instancia del controlador con la conexión
$controller = new ImagenesProductoController($pdo);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (!isset($_GET['producto_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Falta producto_id']);
            exit;
        }
        $imagenes = $controller->obtenerPorProducto($_GET['producto_id']);
        echo json_encode($imagenes);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id'])) {
            // Actualizar imagen existente
            $resultado = $controller->actualizar($data);
        } else {
            // Insertar nueva imagen
            $resultado = $controller->insertar($data);
        }
        echo json_encode($resultado);
        break;

    case 'DELETE':
        // Se espera un id en query param ?id= (pasado en el cuerpo en DELETE)
        parse_str(file_get_contents("php://input"), $delete_vars);
        if (!isset($delete_vars['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Falta id para eliminar']);
            exit;
        }
        $resultado = $controller->eliminar($delete_vars['id']);
        echo json_encode($resultado);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
