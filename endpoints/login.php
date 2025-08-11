<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../models/UsuarioAdmin.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"));
if (!isset($data->correo) || !isset($data->contrasena)) {
    http_response_code(400);
    echo json_encode(["message" => "Se requiere correo y contraseña"]);
    exit;
}

$database = new Database();
$pdo = $database->getConnection();
$usuarioAdmin = new UsuarioAdmin($pdo);

try {
    if (isset($data->action) && $data->action === 'register') {
        // Crear usuario
        if (!isset($data->nombre)) {
            http_response_code(400);
            echo json_encode(["message" => "Se requiere nombre para registro"]);
            exit;
        }

        $usuarioAdmin->nombre = trim($data->nombre);
        $usuarioAdmin->correo = trim($data->correo);
        $usuarioAdmin->contrasena = $data->contrasena;
        $usuarioAdmin->telefono = isset($data->telefono) ? trim($data->telefono) : null;

        if ($usuarioAdmin->crear()) {
            echo json_encode(["success" => true, "message" => "Administrador creado"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error al crear administrador"]);
        }
    } else {
        // Login por defecto
        $resultado = $usuarioAdmin->login($data->correo, $data->contrasena);
        if ($resultado) {
            echo json_encode(["message" => "Login exitoso", "usuario" => $resultado]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Credenciales incorrectas"]);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error interno del servidor"]);
    error_log("Error en usuario_admin.php: " . $e->getMessage());
}
