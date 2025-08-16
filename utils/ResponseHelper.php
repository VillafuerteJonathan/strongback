<?php
/**
 * Clase auxiliar para manejar respuestas HTTP consistentes
 */
class ResponseHelper {
    
    /**
     * Envía una respuesta JSON estandarizada
     */
    public static function sendResponse($success, $message, $data = null, $httpCode = 200) {
        http_response_code($httpCode);
        
        $response = [
            'success' => $success,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
        exit;
    }

    /**
     * Maneja errores de validación
     */
    public static function sendValidationError($message, $errors = []) {
        self::sendResponse(false, $message, ['errors' => $errors], 400);
    }

    /**
     * Maneja errores internos del servidor
     */
    public static function sendServerError($message = 'Error interno del servidor') {
        self::sendResponse(false, $message, null, 500);
    }

    /**
     * Maneja respuestas exitosas
     */
    public static function sendSuccess($message, $data = null) {
        self::sendResponse(true, $message, $data, 200);
    }

    /**
     * Maneja recursos no encontrados
     */
    public static function sendNotFound($message = 'Recurso no encontrado') {
        self::sendResponse(false, $message, null, 404);
    }

    /**
     * Valida campos requeridos en datos POST/PUT
     */
    public static function validateRequiredFields($data, $requiredFields) {
        $missing = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    /**
     * Sanitiza texto de entrada
     */
    public static function sanitize($text) {
        return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
    }
}
