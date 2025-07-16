<?php
// modelos/Denuncias.php

require_once __DIR__ . "/../config/database.php";

class Denuncias {
    public $conn;
    
    // Propiedades del modelo
    public $id_denuncia;
    public $numero_denuncia;
    public $titulo;
    public $descripcion;
    public $id_categoria;
    public $id_usuario_denunciante;
    public $id_estado_denuncia;
    public $id_institucion_asignada;
    public $provincia;
    public $canton;
    public $parroquia;
    public $direccion_especifica;
    public $fecha_ocurrencia;
    public $gravedad;
    public $servidor_municipal;
    public $entidad_municipal;
    public $informacion_adicional_denunciado;
    public $requiere_atencion_prioritaria;
    public $acepta_politica_privacidad;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Crear nueva denuncia
     */
    public function crear() {
        try {
            $query = "INSERT INTO denuncias (
                titulo, descripcion, id_categoria, id_usuario_denunciante,
                provincia, canton, parroquia, direccion_especifica,
                fecha_ocurrencia, gravedad, servidor_municipal, entidad_municipal,
                informacion_adicional_denunciado, requiere_atencion_prioritaria,
                acepta_politica_privacidad
            ) VALUES (
                :titulo, :descripcion, :id_categoria, :id_usuario_denunciante,
                :provincia, :canton, :parroquia, :direccion_especifica,
                :fecha_ocurrencia, :gravedad, :servidor_municipal, :entidad_municipal,
                :informacion_adicional_denunciado, :requiere_atencion_prioritaria,
                :acepta_politica_privacidad
            )";
            
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $this->titulo = htmlspecialchars(strip_tags($this->titulo));
            $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
            $this->provincia = htmlspecialchars(strip_tags($this->provincia));
            $this->canton = htmlspecialchars(strip_tags($this->canton));
            $this->parroquia = htmlspecialchars(strip_tags($this->parroquia));
            $this->direccion_especifica = htmlspecialchars(strip_tags($this->direccion_especifica));
            $this->servidor_municipal = htmlspecialchars(strip_tags($this->servidor_municipal));
            $this->entidad_municipal = htmlspecialchars(strip_tags($this->entidad_municipal));
            $this->informacion_adicional_denunciado = htmlspecialchars(strip_tags($this->informacion_adicional_denunciado));
            
            // Bind parameters
            $stmt->bindParam(':titulo', $this->titulo);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':id_categoria', $this->id_categoria);
            $stmt->bindParam(':id_usuario_denunciante', $this->id_usuario_denunciante);
            $stmt->bindParam(':provincia', $this->provincia);
            $stmt->bindParam(':canton', $this->canton);
            $stmt->bindParam(':parroquia', $this->parroquia);
            $stmt->bindParam(':direccion_especifica', $this->direccion_especifica);
            $stmt->bindParam(':fecha_ocurrencia', $this->fecha_ocurrencia);
            $stmt->bindParam(':gravedad', $this->gravedad);
            $stmt->bindParam(':servidor_municipal', $this->servidor_municipal);
            $stmt->bindParam(':entidad_municipal', $this->entidad_municipal);
            $stmt->bindParam(':informacion_adicional_denunciado', $this->informacion_adicional_denunciado);
            $stmt->bindParam(':requiere_atencion_prioritaria', $this->requiere_atencion_prioritaria, PDO::PARAM_BOOL);
            $stmt->bindParam(':acepta_politica_privacidad', $this->acepta_politica_privacidad, PDO::PARAM_BOOL);
            
            if ($stmt->execute()) {
                $this->id_denuncia = $this->conn->lastInsertId();
                return $this->id_denuncia;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error creando denuncia: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener denuncia por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT d.*, 
                  c.nombre_categoria, c.tipo_principal, c.icono,
                  e.nombre_estado, e.color as estado_color,
                  CONCAT(u.nombres, ' ', u.apellidos) as denunciante_nombre,
                  u.correo as denunciante_correo, u.telefono_contacto,
                  i.nombre_institucion, i.siglas as institucion_siglas
                  FROM denuncias d
                  LEFT JOIN categorias_denuncia c ON d.id_categoria = c.id_categoria
                  LEFT JOIN estados_denuncia e ON d.id_estado_denuncia = e.id_estado_denuncia
                  LEFT JOIN usuarios u ON d.id_usuario_denunciante = u.id_usuario
                  LEFT JOIN instituciones_responsables i ON d.id_institucion_asignada = i.id_institucion
                  WHERE d.id_denuncia = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener denuncias por usuario
     */
    public function obtenerPorUsuario($id_usuario) {
        $query = "SELECT d.*, 
                  c.nombre_categoria, c.tipo_principal, c.icono,
                  e.nombre_estado, e.color as estado_color,
                  i.nombre_institucion
                  FROM denuncias d
                  LEFT JOIN categorias_denuncia c ON d.id_categoria = c.id_categoria
                  LEFT JOIN estados_denuncia e ON d.id_estado_denuncia = e.id_estado_denuncia
                  LEFT JOIN instituciones_responsables i ON d.id_institucion_asignada = i.id_institucion
                  WHERE d.id_usuario_denunciante = :id_usuario
                  ORDER BY d.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todas las denuncias
     */
    public function obtenerTodas() {
        $query = "SELECT d.*, 
                  c.nombre_categoria, c.tipo_principal, c.icono,
                  e.nombre_estado, e.color as estado_color,
                  CONCAT(u.nombres, ' ', u.apellidos) as denunciante_nombre,
                  i.nombre_institucion
                  FROM denuncias d
                  LEFT JOIN categorias_denuncia c ON d.id_categoria = c.id_categoria
                  LEFT JOIN estados_denuncia e ON d.id_estado_denuncia = e.id_estado_denuncia
                  LEFT JOIN usuarios u ON d.id_usuario_denunciante = u.id_usuario
                  LEFT JOIN instituciones_responsables i ON d.id_institucion_asignada = i.id_institucion
                  ORDER BY d.fecha_creacion DESC";
        
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Actualizar estado de denuncia
     */
    public function actualizarEstado($id, $nuevo_estado) {
        $query = "UPDATE denuncias 
                  SET id_estado_denuncia = :nuevo_estado,
                      fecha_actualizacion = CURRENT_TIMESTAMP
                  WHERE id_denuncia = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Validar datos de denuncia
     */
    public function validarDatos($datos) {
        $errores = [];
        
        if (empty($datos['titulo'])) {
            $errores[] = "El título es obligatorio";
        } elseif (strlen($datos['titulo']) < 10) {
            $errores[] = "El título debe tener al menos 10 caracteres";
        }
        
        if (empty($datos['descripcion'])) {
            $errores[] = "La descripción es obligatoria";
        } elseif (strlen($datos['descripcion']) < 50) {
            $errores[] = "La descripción debe tener al menos 50 caracteres";
        }
        
        if (empty($datos['id_categoria'])) {
            $errores[] = "La categoría es obligatoria";
        }
        
        if (empty($datos['provincia'])) {
            $errores[] = "La provincia es obligatoria";
        }
        
        if (empty($datos['canton'])) {
            $errores[] = "El cantón es obligatorio";
        }
        
        if (!isset($datos['acepta_politica_privacidad']) || !$datos['acepta_politica_privacidad']) {
            $errores[] = "Debe aceptar la política de privacidad";
        }
        
        return $errores;
    }
}
?>