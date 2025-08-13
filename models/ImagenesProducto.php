<?php
class ImagenesProducto {
    private $db;
    private $carpetaUploads;

    public function __construct($conexion, $carpetaUploads = null) {
        $this->db = $conexion;
        if ($carpetaUploads === null) {
            $carpetaUploads = __DIR__ . "/../public/imagenes/productos/";
        }
        $this->carpetaUploads = rtrim($carpetaUploads, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_dir($this->carpetaUploads)) {
            if (!mkdir($this->carpetaUploads, 0755, true)) {
                throw new Exception("No se pudo crear la carpeta de uploads.");
            }
        }
        if (!is_writable($this->carpetaUploads)) {
            throw new Exception("La carpeta de uploads no tiene permisos de escritura.");
        }
    }

    public function obtenerPorProducto($productoId) {
        $productoId = (int) $productoId;
        $sql = "SELECT id, url, angulo, orden FROM imagenes_producto WHERE producto_id = :producto_id ORDER BY orden ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':producto_id' => $productoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editarImagenPorOrdenYAngulo($productoId, $orden, $anguloActual, $nuevoArchivo, $nuevoAngulo) {
        $productoId = (int) $productoId;
        $orden = (int) $orden;
        $anguloActual = trim($anguloActual);
        $nuevoAngulo = trim($nuevoAngulo);

        if (!$nuevoArchivo || $nuevoArchivo['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Archivo no válido'];
        }

        // Buscar imagen existente
        $sql = "SELECT id, url FROM imagenes_producto WHERE producto_id = :producto_id AND orden = :orden AND angulo = :angulo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':producto_id' => $productoId,
            ':orden' => $orden,
            ':angulo' => $anguloActual
        ]);
        $imagenActual = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$imagenActual) {
            return ['success' => false, 'message' => 'No se encontró la imagen para editar'];
        }

        // Eliminar archivo físico anterior
        $rutaAnterior = $this->carpetaUploads . basename($imagenActual['url']);
        if (file_exists($rutaAnterior) && is_writable($rutaAnterior)) {
            @unlink($rutaAnterior);
        }

        // Validar extensión
        $ext = strtolower(pathinfo($nuevoArchivo['name'], PATHINFO_EXTENSION));
        $extPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $extPermitidas)) {
            return ['success' => false, 'message' => 'Tipo de archivo no permitido'];
        }

        // Guardar nuevo archivo
        $nombreNuevo = uniqid() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '', basename($nuevoArchivo['name']));
        $destino = $this->carpetaUploads . $nombreNuevo;
        if (!move_uploaded_file($nuevoArchivo['tmp_name'], $destino)) {
            return ['success' => false, 'message' => 'Error al mover el archivo subido'];
        }

        // Actualizar DB con nueva url y ángulo
        $sqlUpdate = "UPDATE imagenes_producto SET url = :url, angulo = :angulo WHERE id = :id";
        $stmtUpdate = $this->db->prepare($sqlUpdate);
        $resultado = $stmtUpdate->execute([
            ':url' => 'imagenes/productos/' . $nombreNuevo,
            ':angulo' => $nuevoAngulo,
            ':id' => $imagenActual['id']
        ]);

        if (!$resultado) {
            return ['success' => false, 'message' => 'Error al actualizar la base de datos'];
        }

        return ['success' => true, 'message' => 'Imagen actualizada correctamente'];
    }
}
?>
