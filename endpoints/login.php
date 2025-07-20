<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Respondemos rápido a preflight sin hacer nada más
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

// Crear instancia de Database y obtener conexión PDO
$database = new Database();
$pdo = $database->getConnection();

// Crear el objeto UsuarioAdmin con la conexión
$usuario = new UsuarioAdmin($pdo);

$resultado = $usuario->login($data->correo, $data->contrasena);

if ($resultado) {
    echo json_encode(["message" => "Login exitoso", "usuario" => $resultado]);
} else {
    http_response_code(401);
    echo json_encode(["message" => "Credenciales incorrectas"]);
}
