<?php
class UsuarioAdmin {
    private $conn;
    private $table = "usuario_admin";

    public $id;
    public $nombre;
    public $correo;
    public $contrasena; // Hash de contraseña
    public $telefono;
    public $creado_en;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear nuevo admin (guarda hash de contraseña)
    public function crear() {
        // Validar campos obligatorios
        if (empty($this->nombre) || empty($this->correo) || empty($this->contrasena)) {
            return false; // O lanzar excepción según diseño
        }

        // Verificar si correo ya existe
        if ($this->buscarPorCorreo()) {
            return false; // Ya existe usuario con ese correo
        }

        try {
            $query = "INSERT INTO {$this->table} (nombre, correo, contrasena, telefono) VALUES (:nombre, :correo, :contrasena, :telefono)";
            $stmt = $this->conn->prepare($query);

            // Encriptar password
            $hash = password_hash($this->contrasena, PASSWORD_BCRYPT);

            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->bindParam(':contrasena', $hash);
            $stmt->bindParam(':telefono', $this->telefono);

            $result = $stmt->execute();
            $stmt->closeCursor();
            return $result;

        } catch (PDOException $e) {
            // Log o manejo de error
            error_log("Error al crear admin: " . $e->getMessage());
            return false;
        }
    }

    // Buscar admin por correo (para login)
    public function buscarPorCorreo() {
        try {
            $query = "SELECT * FROM {$this->table} WHERE correo = :correo LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $usuario;
        } catch (PDOException $e) {
            error_log("Error buscarPorCorreo: " . $e->getMessage());
            return false;
        }
    }

    // Login: verifica correo y contraseña
    public function login($correo, $contrasena) {
        $this->correo = $correo;
        $usuario = $this->buscarPorCorreo();

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            unset($usuario['contrasena']); // Quitar hash para no exponer
            return $usuario;
        }

        return false;
    }
}
