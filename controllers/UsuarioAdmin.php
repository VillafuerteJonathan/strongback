<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../config/database.php');

require_once(__DIR__ .'/../models/UsuarioAdmin.php');

$db = (new Database())->getConnection();
$usuarioAdmin = new UsuarioAdmin($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Crear nuevo admin (ejemplo)
    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data['nombre']) && !empty($data['correo']) && !empty($data['contrasena'])) {
        $usuarioAdmin->nombre = $data['nombre'];
        $usuarioAdmin->correo = $data['correo'];
        $usuarioAdmin->contrasena = $data['contrasena'];
        $usuarioAdmin->telefono = $data['telefono'] ?? null;

        if ($usuarioAdmin->crear()) {
            echo json_encode(['success' => true, 'message' => 'Administrador creado']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al crear administrador']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
    }
} else if ($method === 'GET') {
    // Buscar admin por correo (ejemplo)
    if (isset($_GET['correo'])) {
        $usuarioAdmin->correo = $_GET['correo'];
        $admin = $usuarioAdmin->buscarPorCorreo();

        if ($admin) {
            // No mostrar la contraseña
            unset($admin['contrasena']);
            echo json_encode($admin);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Administrador no encontrado']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Se requiere parámetro correo']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
