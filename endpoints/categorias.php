<?php
// Permitir solicitudes CORS desde cualquier origen (puedes ajustar el dominio en lugar de '*')
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-HTTP-Method-Override");
header("Content-Type: application/json; charset=UTF-8");

// Responder rápido a preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../controllers/Categorias.php';

$database = new Database();
$pdo = $database->getConnection();
$controller = new CategoriaController($pdo);

// Detectar método (con soporte para X-HTTP-Method-Override)
$method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['id'])) {
            $data = $controller->obtenerPorId(intval($_GET['id']));
            if ($data) {
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Categoría no encontrada"]);
            }
        } else {
            echo json_encode($controller->obtenerTodas());
        }
        break;

    case 'POST':
        // Aquí definimos las dos acciones: crear y actualizar según un parámetro 'action'
        $action = $_POST['action'] ?? 'create';

        if ($action === 'create') {
            if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(["message" => "Imagen requerida"]);
                exit;
            }

            $nombre = $_POST['nombre'] ?? null;
            $descripcion = $_POST['descripcion'] ?? null;

            if (!$nombre) {
                http_response_code(400);
                echo json_encode(["message" => "El nombre es requerido"]);
                exit;
            }

            $check = getimagesize($_FILES["imagen"]["tmp_name"]);
            if ($check === false) {
                http_response_code(400);
                echo json_encode(["message" => "El archivo no es una imagen válida"]);
                exit;
            }

            $allowedTypes = ['image/jpeg', 'image/png'];
            if (!in_array($_FILES['imagen']['type'], $allowedTypes)) {
                http_response_code(400);
                echo json_encode(["message" => "Solo se permiten imágenes JPG o PNG"]);
                exit;
            }

            $targetDir = __DIR__ . '/../public/imagenes/caracteristicas/';
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $ext;
            $targetFile = $targetDir . $fileName;

            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
                http_response_code(500);
                echo json_encode(["message" => "Error al subir la imagen"]);
                exit;
            }

            $imagen_url = 'imagenes/caracteristicas/' . $fileName;

            $data = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'imagen_url' => $imagen_url
            ];

            $result = $controller->crear($data);

            if (!$result) {
                http_response_code(500);
                echo json_encode(["message" => "Error al crear categoría"]);
            } else {
                http_response_code(201);
                echo json_encode([
                    "message" => "Categoría creada",
                    "imagen_url" => $imagen_url
                ]);
            }
        } elseif ($action === 'update') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;

    if (!$id || !$nombre) {
        http_response_code(400);
        echo json_encode(["message" => "ID y nombre son requeridos"]);
        exit;
    }

    $categoriaActual = $controller->obtenerPorId(intval($id));
    if (!$categoriaActual) {
        http_response_code(404);
        echo json_encode(["message" => "Categoría no encontrada"]);
        exit;
    }

    // Inicialmente usar la imagen actual
    $imagen_url = $categoriaActual['imagen_url'];

    // Si se subió una nueva imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $check = getimagesize($_FILES["imagen"]["tmp_name"]);
        if ($check === false) {
            http_response_code(400);
            echo json_encode(["message" => "El archivo no es una imagen válida"]);
            exit;
        }

        $allowedTypes = ['image/jpeg', 'image/png'];
        if (!in_array($_FILES['imagen']['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(["message" => "Solo se permiten imágenes JPG o PNG"]);
            exit;
        }

        $targetDir = __DIR__ . '/../public/imagenes/caracteristicas/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        $targetFile = $targetDir . $fileName;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
            http_response_code(500);
            echo json_encode(["message" => "Error al subir la imagen"]);
            exit;
        }

        $imagen_url = 'imagenes/caracteristicas/' . $fileName;

        // Borrar la imagen anterior si existe
        if (!empty($categoriaActual['imagen_url'])) {
            $imagenAntiguaPath = __DIR__ . '/../public/' . $categoriaActual['imagen_url'];
            if (file_exists($imagenAntiguaPath)) {
                unlink($imagenAntiguaPath);
            }
        }
    }

    $data = [
        'id' => intval($id),
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'imagen_url' => $imagen_url // Siempre incluir la imagen actual o nueva
    ];

    $result = $controller->actualizar($data);

    if (!$result) {
        http_response_code(500);
        echo json_encode(["message" => "Error al actualizar"]);
    } else {
        echo json_encode(["message" => "Categoría actualizada correctamente"]);
    }
}
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["message" => "ID es requerido para eliminar"]);
            break;
        }

        $result = $controller->eliminar(intval($_GET['id']));
        if (!$result) {
            http_response_code(500);
            echo json_encode(["message" => "Error al eliminar"]);
        } else {
            echo json_encode(["message" => "Categoría eliminada"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método no permitido"]);
        break;
}
