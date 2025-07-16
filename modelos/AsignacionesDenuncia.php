<?php
// modelos/AsignacionesDenuncia.php

require_once __DIR__ . "/../config/database.php";

class AsignacionesDenuncia {
    public $conn;
    
    public $id_asignacion;
    public $id_denuncia;
    public $id_usuario_supervisor;
    public $id_institucion_asignada;
    public $comentario_asignacion;
    public $prioridad;
    public $estado_asignacion;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Crear nueva asignación
     */
    public function crear() {
        $query = "INSERT INTO asignaciones_denuncia (
                    id_denuncia, id_usuario_supervisor, id_institucion_asignada,
                    comentario_asignacion, prioridad
                  ) VALUES (
                    :id_denuncia, :id_usuario_supervisor, :id_institucion_asignada,
                    :comentario_asignacion, :prioridad
                  )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $this->id_denuncia, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario_supervisor', $this->id_usuario_supervisor, PDO::PARAM_INT);
        $stmt->bindParam(':id_institucion_asignada', $this->id_institucion_asignada, PDO::PARAM_INT);
        $stmt->bindParam(':comentario_asignacion', $this->comentario_asignacion);
        $stmt->bindParam(':prioridad', $this->prioridad);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Obtener asignación activa por denuncia
     */
    public function obtenerActivaPorDenuncia($id_denuncia) {
        $query = "SELECT a.*, 
                         i.nombre_institucion, i.siglas as institucion_siglas,
                         CONCAT(u.nombres, ' ', u.apellidos) as supervisor_nombre
                  FROM asignaciones_denuncia a
                  LEFT JOIN instituciones_responsables i ON a.id_institucion_asignada = i.id_institucion
                  LEFT JOIN usuarios u ON a.id_usuario_supervisor = u.id_usuario
                  WHERE a.id_denuncia = :id_denuncia AND a.estado_asignacion = 'ACTIVO'
                  ORDER BY a.fecha_asignacion DESC
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todas las asignaciones por denuncia
     */
    public function obtenerPorDenuncia($id_denuncia) {
        $query = "SELECT a.*, 
                         i.nombre_institucion, i.siglas as institucion_siglas,
                         CONCAT(u.nombres, ' ', u.apellidos) as supervisor_nombre
                  FROM asignaciones_denuncia a
                  LEFT JOIN instituciones_responsables i ON a.id_institucion_asignada = i.id_institucion
                  LEFT JOIN usuarios u ON a.id_usuario_supervisor = u.id_usuario
                  WHERE a.id_denuncia = :id_denuncia
                  ORDER BY a.fecha_asignacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cambiar estado de asignación
     */
    public function cambiarEstado($id, $nuevo_estado) {
        $query = "UPDATE asignaciones_denuncia 
                  SET estado_asignacion = :nuevo_estado 
                  WHERE id_asignacion = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nuevo_estado', $nuevo_estado);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Obtener denuncias asignadas a una institución
     */
    public function obtenerPorInstitucion($id_institucion) {
        $query = "SELECT a.*, d.numero_denuncia, d.titulo, d.gravedad,
                         e.nombre_estado, e.color as estado_color,
                         CONCAT(u.nombres, ' ', u.apellidos) as denunciante_nombre
                  FROM asignaciones_denuncia a
                  INNER JOIN denuncias d ON a.id_denuncia = d.id_denuncia
                  LEFT JOIN estados_denuncia e ON d.id_estado_denuncia = e.id_estado_denuncia
                  LEFT JOIN usuarios u ON d.id_usuario_denunciante = u.id_usuario
                  WHERE a.id_institucion_asignada = :id_institucion 
                  AND a.estado_asignacion = 'ACTIVO'
                  ORDER BY a.fecha_asignacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>