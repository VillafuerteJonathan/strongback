<?php
header('Content-Type: application/json');

// Permitir CORS si necesitas que React u otro frontend acceda desde otro dominio
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Responder a preflight OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Obtenemos la ruta por GET: ?ruta=usuario_admin, ?ruta=productos, etc
$ruta = $_GET['ruta'] ?? '';

// Mapea rutas a archivos controladores
$rutas_validas = [
    'usuario_admin' => 'controllers/UsuarioAdmin.php',
];

if (array_key_exists($ruta, $rutas_validas)) {
    require_once $rutas_validas[$ruta];
} else {
    http_response_code(404);
    echo json_encode([
        'error' => true,
        'message' => 'Ruta no encontrada. Usa una ruta válida: ' . implode(', ', array_keys($rutas_validas))
    ]);
}
