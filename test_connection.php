<?php
require_once './config/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "Conexión exitosa a la base de datos.";
} else {
    echo "Error al conectar con la base de datos.";
}
