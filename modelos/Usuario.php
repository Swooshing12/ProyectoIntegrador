<?php
require_once __DIR__ . "/../config/database.php";

class Usuario {
    public $conn;

    public $id_usuario;
    public $cedula;
    public $username;
    public $nombres;
    public $apellidos;
    public $sexo;
    public $nacionalidad;
    public $telefono_contacto;      // ✅ CAMPO EXISTENTE
    public $direccion_domicilio;    // ✅ CAMPO EXISTENTE
    public $fecha_verificacion;     // ✅ NUEVO CAMPO AÑADIDO
    public $correo;
    public $password;
    public $id_rol;
    public $id_estado;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * 🔹 Obtener usuario por username
     */
    public function obtenerPorUsername(string $username): ?array {
        $query = "SELECT * FROM usuarios WHERE username = :username LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * 🔹 Obtener usuario por correo
     */
    public function obtenerPorCorreo(string $correo): ?array {
        $query = "SELECT * FROM usuarios WHERE correo = :correo LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":correo", $correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * 🔹 Crear un nuevo usuario (ACTUALIZADO con todos los campos incluyendo fecha_verificacion)
     */
    public function crearUsuario(
        int    $cedula,
        string $username,
        string $nombres,
        string $apellidos,
        string $sexo,
        string $nacionalidad,
        string $telefono_contacto,    // ✅ PARÁMETRO EXISTENTE
        string $direccion_domicilio,  // ✅ PARÁMETRO EXISTENTE
        string $correo,
        string $password,
        int    $id_rol,
        string $fecha_verificacion = null  // ✅ NUEVO PARÁMETRO
    ): bool {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $estadoPendiente = 3;

        $query = "INSERT INTO usuarios 
            (cedula, username, nombres, apellidos, sexo, nacionalidad, telefono_contacto, direccion_domicilio, fecha_verificacion, correo, password, id_rol, id_estado)
          VALUES
            (:cedula, :username, :nombres, :apellidos, :sexo, :nacionalidad, :telefono_contacto, :direccion_domicilio, :fecha_verificacion, :correo, :password, :id_rol, :id_estado)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cedula",             $cedula,             PDO::PARAM_INT);
        $stmt->bindParam(":username",           $username);
        $stmt->bindParam(":nombres",            $nombres);
        $stmt->bindParam(":apellidos",          $apellidos);
        $stmt->bindParam(":sexo",               $sexo);
        $stmt->bindParam(":nacionalidad",       $nacionalidad);
        $stmt->bindParam(":telefono_contacto",  $telefono_contacto);
        $stmt->bindParam(":direccion_domicilio", $direccion_domicilio);
        $stmt->bindParam(":fecha_verificacion", $fecha_verificacion); // ✅ NUEVO BIND
        $stmt->bindParam(":correo",             $correo);
        $stmt->bindParam(":password",           $hash);
        $stmt->bindParam(":id_rol",             $id_rol,             PDO::PARAM_INT);
        $stmt->bindParam(":id_estado",          $estadoPendiente,    PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * 🔹 Editar usuario (ACTUALIZADO con todos los campos incluyendo fecha_verificacion)
     */
    public function editarUsuario(
        int    $id_usuario,
        int    $cedula,
        string $username,
        string $nombres,
        string $apellidos,
        string $sexo,
        string $nacionalidad,
        string $telefono_contacto,    // ✅ PARÁMETRO EXISTENTE
        string $direccion_domicilio,  // ✅ PARÁMETRO EXISTENTE
        string $correo,
        int    $id_rol,
        int    $id_estado,
        string $fecha_verificacion = null // ✅ NUEVO PARÁMETRO
    ): bool {
        $query = "UPDATE usuarios SET
            cedula             = :cedula,
            username           = :username,
            nombres            = :nombres,
            apellidos          = :apellidos,
            sexo               = :sexo,
            nacionalidad       = :nacionalidad,
            telefono_contacto  = :telefono_contacto,
            direccion_domicilio = :direccion_domicilio,
            fecha_verificacion = :fecha_verificacion, -- ✅ NUEVO CAMPO
            correo             = :correo,
            id_rol             = :id_rol,
            id_estado          = :id_estado
          WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cedula",             $cedula,             PDO::PARAM_INT);
        $stmt->bindParam(":username",           $username);
        $stmt->bindParam(":nombres",            $nombres);
        $stmt->bindParam(":apellidos",          $apellidos);
        $stmt->bindParam(":sexo",               $sexo);
        $stmt->bindParam(":nacionalidad",       $nacionalidad);
        $stmt->bindParam(":telefono_contacto",  $telefono_contacto);
        $stmt->bindParam(":direccion_domicilio", $direccion_domicilio);
        $stmt->bindParam(":fecha_verificacion", $fecha_verificacion); // ✅ NUEVO BIND
        $stmt->bindParam(":correo",             $correo);
        $stmt->bindParam(":id_rol",             $id_rol,             PDO::PARAM_INT);
        $stmt->bindParam(":id_estado",          $id_estado,          PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario",         $id_usuario,         PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * 🔹 Cambiar contraseña
     */
    public function cambiarPassword(int $id_usuario, string $newPassword): bool {
        $hash  = password_hash($newPassword, PASSWORD_BCRYPT);
        $query = "UPDATE usuarios SET password = :password WHERE id_usuario = :id_usuario";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":password",   $hash);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * 🔹 Eliminar usuario
     */
    public function eliminarUsuario(int $id_usuario): bool {
        $query = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * 🔹 Desactivar usuario (cambiar id_estado a 4)
     */
    public function desactivarUsuario(int $id_usuario): bool {
        $estadoDesactivado = 4;
        $query = "UPDATE usuarios SET id_estado = :estado WHERE id_usuario = :id_usuario";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $estadoDesactivado, PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * 🔹 Login flexible: acepta username o correo
     */
    public function login(string $identifier, string $password): mixed {
        $esEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        $usuario = $esEmail
            ? $this->obtenerPorCorreo($identifier)
            : $this->obtenerPorUsername($identifier);

        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }

    /**
     * 🔹 Bloquear usuario (cambiar estado)
     */
    public function bloquearUsuario(int $id_usuario): bool {
        $query = "UPDATE usuarios SET id_estado = 2 WHERE id_usuario = :id_usuario";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * 🔹 Actualizar estado genérico
     */
    public function actualizarEstado(int $id_usuario, int $nuevo_estado): bool {
        $query = "UPDATE usuarios SET id_estado = :id_estado WHERE id_usuario = :id_usuario";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id_estado",  $nuevo_estado, PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario", $id_usuario,   PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * 🔹 Obtener todos los usuarios
     */
    public function obtenerTodos(int $id_estado = null): array {
        if ($id_estado !== null) {
            $query = "SELECT 
                        id_usuario, cedula, username, nombres, apellidos,
                        sexo, nacionalidad, telefono_contacto, direccion_domicilio, 
                        fecha_verificacion, correo, id_rol, id_estado
                      FROM usuarios
                      WHERE id_estado = :estado";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['estado' => $id_estado]);
        } else {
            $query = "SELECT 
                        id_usuario, cedula, username, nombres, apellidos,
                        sexo, nacionalidad, telefono_contacto, direccion_domicilio, 
                        fecha_verificacion, correo, id_rol, id_estado
                      FROM usuarios";
            $stmt = $this->conn->query($query);
        }
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 🔹 Obtener usuario por ID
     */
    public function obtenerPorId(int $id_usuario): ?array {
        $query = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * 🔹 Contar usuarios filtrados por estado
     */
    public function contarUsuarios($estado = null, $busqueda = '') {
        $sql = "SELECT COUNT(*) as total FROM usuarios u WHERE 1=1";
        $params = [];
        
        if ($estado !== null) {
            $sql .= " AND u.id_estado = ?";
            $params[] = $estado;
        }
        
        if (!empty($busqueda)) {
            $sql .= " AND (u.cedula LIKE ? OR u.username LIKE ? OR u.nombres LIKE ? 
                      OR u.apellidos LIKE ? OR u.correo LIKE ?)";
            $termino = "%{$busqueda}%";
            $params = array_merge($params, [$termino, $termino, $termino, $termino, $termino]);
        }
        
        error_log("DEBUG contarUsuarios SQL: $sql");
        error_log("DEBUG contarUsuarios PARAMS: " . json_encode($params));
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $total = (int)$resultado['total'];
            
            error_log("DEBUG contarUsuarios RESULTADO: $total");
            return $total;
        } catch (PDOException $e) {
            error_log("ERROR en contarUsuarios: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 🔹 Obtener usuarios paginados
     */
    public function obtenerUsuariosPaginados($estado = null, $offset = 0, $limit = 10, $busqueda = '') {
        $sql = "SELECT u.* FROM usuarios u WHERE 1=1";
        $params = [];
        
        if ($estado !== null) {
            $sql .= " AND u.id_estado = ?";
            $params[] = $estado;
        }
        
        if (!empty($busqueda)) {
            $sql .= " AND (u.cedula LIKE ? OR u.username LIKE ? OR u.nombres LIKE ? 
                      OR u.apellidos LIKE ? OR u.correo LIKE ?)";
            $termino = "%{$busqueda}%";
            $params = array_merge($params, [$termino, $termino, $termino, $termino, $termino]);
        }
        
        $offset = (int)$offset;
        $limit = (int)$limit;
        
        $sql .= " ORDER BY u.id_usuario DESC LIMIT {$offset}, {$limit}";
        
        error_log("DEBUG SQL FINAL: $sql");
        error_log("DEBUG PARAMS: " . json_encode($params));
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("DEBUG: " . count($resultados) . " usuarios encontrados");
            return $resultados;
        } catch (PDOException $e) {
            error_log("ERROR SQL: " . $e->getMessage());
            error_log("SQL que falló: $sql");
            throw $e;
        }
    }

    /**
     * 🔹 Contar usuarios por rol específico
     */
    public function contarUsuariosPorRol($id_rol) {
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE id_rol = :id_rol";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$resultado['total'];
        } catch (PDOException $e) {
            error_log("Error contando usuarios por rol: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * 🔹 Crear usuario con array de datos (ACTUALIZADO con fecha_verificacion)
     */
    public function crear(array $datos): int {
        try {
            $query = "INSERT INTO usuarios 
                (cedula, username, nombres, apellidos, sexo, nacionalidad, telefono_contacto, direccion_domicilio, fecha_verificacion, correo, password, id_rol, id_estado)
              VALUES
                (:cedula, :username, :nombres, :apellidos, :sexo, :nacionalidad, :telefono_contacto, :direccion_domicilio, :fecha_verificacion, :correo, :password, :id_rol, :id_estado)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':cedula' => $datos['cedula'],
                ':username' => $datos['username'],
                ':nombres' => $datos['nombres'],
                ':apellidos' => $datos['apellidos'],
                ':sexo' => $datos['sexo'],
                ':nacionalidad' => $datos['nacionalidad'],
                ':telefono_contacto' => $datos['telefono_contacto'] ?? '',
                ':direccion_domicilio' => $datos['direccion_domicilio'] ?? '',
                ':fecha_verificacion' => $datos['fecha_verificacion'] ?? null, // ✅ NUEVO CAMPO
                ':correo' => $datos['correo'],
                ':password' => $datos['password'],
                ':id_rol' => $datos['id_rol'],
                ':id_estado' => $datos['id_estado']
            ]);
            
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creando usuario: " . $e->getMessage());
            throw new Exception("Error al crear el usuario");
        }
    }

    /**
     * 🔹 Verificar si existe usuario por cédula
     */
    public function existeUsuarioPorCedula(int $cedula): bool {
        try {
            $query = "SELECT COUNT(*) as total FROM usuarios WHERE cedula = :cedula";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':cedula' => $cedula]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error verificando cédula: " . $e->getMessage());
            throw new Exception("Error al verificar cédula");
        }
    }

    /**
     * 🔹 Verificar si existe usuario por correo (con exclusión opcional)
     */
    public function existeUsuarioPorCorreo(string $correo, int $id_excluir = null): bool {
        try {
            $query = "SELECT COUNT(*) as total FROM usuarios WHERE correo = :correo";
            $params = [':correo' => $correo];
            
            if ($id_excluir !== null) {
                $query .= " AND id_usuario != :id_excluir";
                $params[':id_excluir'] = $id_excluir;
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error verificando correo: " . $e->getMessage());
            throw new Exception("Error al verificar correo");
        }
    }

    /**
     * 🔹 Verificar si existe usuario por username (con exclusión opcional)
     */
    public function existeUsuarioPorUsername(string $username, int $id_excluir = null): bool {
        try {
            $query = "SELECT COUNT(*) as total FROM usuarios WHERE username = :username";
            $params = [':username' => $username];
            
            if ($id_excluir !== null) {
                $query .= " AND id_usuario != :id_excluir";
                $params[':id_excluir'] = $id_excluir;
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error verificando username: " . $e->getMessage());
            throw new Exception("Error al verificar username");
        }
    }

    /**
     * 🔹 Obtener usuario por cédula
     */
    public function obtenerPorCedula(int $cedula): ?array {
        $query = "SELECT * FROM usuarios WHERE cedula = :cedula LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":cedula", $cedula, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * 🔹 Actualizar contraseña de un usuario
     */
    public function actualizarPassword($id_usuario, $nueva_password) {
        try {
            $query = "UPDATE usuarios SET password = :password WHERE id_usuario = :id_usuario";
            
            $stmt = $this->conn->prepare($query);
            $resultado = $stmt->execute([
                ':password' => $nueva_password,
                ':id_usuario' => $id_usuario
            ]);
            
            if ($resultado) {
                error_log("✅ Contraseña actualizada para usuario ID: $id_usuario");
                return true;
            } else {
                error_log("❌ Error actualizando contraseña para usuario ID: $id_usuario");
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Error actualizando contraseña: " . $e->getMessage());
            throw new Exception("Error al actualizar la contraseña");
        }
    }

    /**
     * 🔹 Verificar cuenta de usuario (actualizar fecha_verificacion)
     */
    public function verificarCuenta(int $id_usuario): bool {
        try {
            $fechaActual = date('Y-m-d H:i:s');
            $query = "UPDATE usuarios SET 
                        fecha_verificacion = :fecha_verificacion,
                        id_estado = 1 
                      WHERE id_usuario = :id_usuario";
            
            $stmt = $this->conn->prepare($query);
            $resultado = $stmt->execute([
                ':fecha_verificacion' => $fechaActual,
                ':id_usuario' => $id_usuario
            ]);
            
            if ($resultado) {
                error_log("✅ Cuenta verificada para usuario ID: $id_usuario");
                return true;
            } else {
                error_log("❌ Error verificando cuenta para usuario ID: $id_usuario");
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Error verificando cuenta: " . $e->getMessage());
            throw new Exception("Error al verificar la cuenta");
        }
    }

    /**
     * 🔹 Actualizar fecha de verificación específica
     */
    public function actualizarFechaVerificacion(int $id_usuario, string $fecha_verificacion = null): bool {
        try {
            if ($fecha_verificacion === null) {
                $fecha_verificacion = date('Y-m-d H:i:s');
            }
            
            $query = "UPDATE usuarios SET fecha_verificacion = :fecha_verificacion WHERE id_usuario = :id_usuario";
            
            $stmt = $this->conn->prepare($query);
            $resultado = $stmt->execute([
                ':fecha_verificacion' => $fecha_verificacion,
                ':id_usuario' => $id_usuario
            ]);
            
            if ($resultado) {
                error_log("✅ Fecha de verificación actualizada para usuario ID: $id_usuario");
                return true;
            } else {
                error_log("❌ Error actualizando fecha de verificación para usuario ID: $id_usuario");
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Error actualizando fecha de verificación: " . $e->getMessage());
            throw new Exception("Error al actualizar la fecha de verificación");
        }
    }

    /**
     * 🔹 Verificar si usuario está verificado
     */
    public function estaVerificado(int $id_usuario): bool {
        try {
            $query = "SELECT fecha_verificacion FROM usuarios WHERE id_usuario = :id_usuario";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id_usuario' => $id_usuario]);
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado && $resultado['fecha_verificacion'] !== null;
            
        } catch (PDOException $e) {
            error_log("Error verificando estado de verificación: " . $e->getMessage());
            return false;
        }
    }
    // Añadir estos métodos a la clase Usuario existente


/**
 * Actualizar contraseña y estado del usuario
 */
public function actualizarPasswordYEstado($id_usuario, $password_hash, $id_estado) {
    try {
        $query = "UPDATE usuarios 
                  SET password = :password, id_estado = :id_estado 
                  WHERE id_usuario = :id_usuario";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
        $stmt->bindParam(':id_estado', $id_estado, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error actualizando contraseña y estado: " . $e->getMessage());
        return false;
    }
}
}
?>