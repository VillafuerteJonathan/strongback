<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../controllers/ImagenesProductoController.php';

$database = new Database();
$pdo = $database->getConnection();
$controller = new ImagenesProductoController($pdo);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (!isset($_GET['producto_id']) || !is_numeric($_GET['producto_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Falta o es inválido producto_id']);
        exit;
    }
    $imagenes = $controller->obtenerPorProducto((int)$_GET['producto_id']);
    echo json_encode($imagenes);
    exit;
}

if ($method === 'POST') {
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

    if (stripos($contentType, 'multipart/form-data') !== false) {
        $action = $_POST['action'] ?? '';

        if ($action === 'update') {
            $camposRequeridos = ['producto_id', 'orden', 'angulo_actual', 'nuevo_angulo'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Falta campo $campo"]);
                    exit;
                }
            }

            if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No se envió archivo válido de imagen']);
                exit;
            }

            $resultado = $controller->editarImagenPorOrdenYAngulo(
                $_POST['producto_id'],
                $_POST['orden'],
                $_POST['angulo_actual'],
                $_FILES['imagen'],
                $_POST['nuevo_angulo']
            );

            if ($resultado['success']) {
                echo json_encode($resultado);
            } else {
                http_response_code(400);
                echo json_encode($resultado);
            }
            exit;
        }

        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Acción no soportada']);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Contenido no soportado, use multipart/form-data']);
        exit;
    }
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Método no permitido']);
exit;
?>
