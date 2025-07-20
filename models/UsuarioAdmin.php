<?php
class UsuarioAdmin {
    private $conn;
    private $table = "usuario_admin";

    public $id;
    public $nombre;
    public $correo;
    public $contrasena; // Aquí se almacena el hash
    public $telefono;
    public $creado_en;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear nuevo admin (guarda hash de contraseña)
    public function crear() {
        $query = "INSERT INTO {$this->table} (nombre, correo, contrasena, telefono) VALUES (:nombre, :correo, :contrasena, :telefono)";
        $stmt = $this->conn->prepare($query);

        // Encriptar password con password_hash antes de asignar
        $hash = password_hash($this->contrasena, PASSWORD_BCRYPT);

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':contrasena', $hash);
        $stmt->bindParam(':telefono', $this->telefono);

        return $stmt->execute();
    }

    // Buscar admin por correo (para login)
    public function buscarPorCorreo() {
        $query = "SELECT * FROM {$this->table} WHERE correo = :correo LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     public function login($correo, $contrasena) {
        $this->correo = $correo;
        $usuario = $this->buscarPorCorreo();

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            unset($usuario['contrasena']); // Elimina el hash del resultado
            return $usuario;
        }

        return false;
    }
}
