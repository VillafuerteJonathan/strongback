<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST,PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-HTTP-Method-Override");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/Productos.php';

$database = new Database();
$db = $database->getConnection();

$controller = new ProductosController($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Si se pasa producto_id en GET, devolver materiales, sino todos productos con todo
    if (isset($_GET['producto_id'])) {
        $productoId = intval($_GET['producto_id']);
        $materiales = $controller->obtenerMateriales($productoId);
        echo json_encode($materiales);
    } else {
        $productos = $controller->obtenerTodos();
        echo json_encode($productos);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Detectar si es edición o creación según si viene id
    $id = $_POST['id'] ?? null;

    // Campos comunes
    $nombre = $_POST['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? null;
    $categoria_id = $_POST['categoria_id'] ?? null;
    $disponible = isset($_POST['disponible']) && ($_POST['disponible'] == 1 || $_POST['disponible'] === 'true') ? 1 : 0;
    $talla_desde = $_POST['talla_desde'] ?? null;
    $talla_hasta = $_POST['talla_hasta'] ?? null;

    if (!$nombre || !$precio || !$categoria_id || !$talla_desde || !$talla_hasta) {
        http_response_code(400);
        echo json_encode(['message' => 'Faltan campos obligatorios']);
        exit;
    }

    // Materiales opcionales
    $material = $_POST['material'] ?? null;
    $suela = $_POST['suela'] ?? null;
    $forro = $_POST['forro'] ?? null;
    $puntera = $_POST['puntera'] ?? null;
    $plantilla = $_POST['plantilla'] ?? null;

    // Procesar imagen principal - en edición puede no enviarse nueva imagen
    $uploadDir = __DIR__ . '/../public/imagenes/productos/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $imagen_principal_url = $_POST['imagen_principal'] ?? null; // por si no hay archivo nuevo

    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $fileExt = pathinfo($_FILES['imagen_principal']['name'], PATHINFO_EXTENSION);
        $fileNamePrincipal = uniqid() . '.' . $fileExt;
        $targetFilePrincipal = $uploadDir . $fileNamePrincipal;

        if (!move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $targetFilePrincipal)) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al subir la imagen principal']);
            exit;
        }
        $imagen_principal_url = 'imagenes/productos/' . $fileNamePrincipal;
    }

    // Imágenes adicionales (en creación solo, edición deberías hacer endpoint aparte)
    $angulos = ['superior', 'planta', 'lateral'];
    $imagenesUrls = [];
    if (!$id) { // Solo insertar imágenes adicionales en creación
        foreach ($angulos as $angulo) {
            if (isset($_FILES["imagen_$angulo"]) && $_FILES["imagen_$angulo"]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES["imagen_$angulo"]['name'], PATHINFO_EXTENSION);
                $fileName = uniqid() . '.' . $ext;
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES["imagen_$angulo"]['tmp_name'], $targetFile)) {
                    $imagenesUrls[$angulo] = 'imagenes/productos/' . $fileName;
                }
            }
        }
    }

    $data = [
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'precio' => $precio,
        'categoria_id' => $categoria_id,
        'disponible' => $disponible,
        'imagen_principal' => $imagen_principal_url,
        'talla_desde' => $talla_desde,
        'talla_hasta' => $talla_hasta,
        'material' => $material,
        'suela' => $suela,
        'forro' => $forro,
        'puntera' => $puntera,
        'plantilla' => $plantilla,
    ];

    if ($id) {
        $editado = $controller->editar($id, $data);
        if ($editado) {
            http_response_code(200);
            echo json_encode(['message' => 'Producto editado correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al editar producto']);
        }
    } else {
        $creado = $controller->crear($data, $imagenesUrls);
        if ($creado) {
            http_response_code(201);
            echo json_encode(['message' => 'Producto creado correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al crear producto']);
        }
    }
    exit();
}

// Si no es GET ni POST válido
http_response_code(405);
echo json_encode(['message' => 'Método no permitido']);
