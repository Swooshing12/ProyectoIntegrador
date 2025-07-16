<?php
// modelos/SeguimientoDenuncias.php

require_once __DIR__ . "/../config/database.php";

class SeguimientoDenuncias {
    public $conn;
    
    public $id_seguimiento;
    public $id_denuncia;
    public $id_estado_anterior;
    public $id_estado_nuevo;
    public $id_usuario_responsable;
    public $comentario;
    public $es_visible_denunciante;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Crear nuevo seguimiento
     */
    public function crear() {
        $query = "INSERT INTO seguimiento_denuncias (
                    id_denuncia, id_estado_anterior, id_estado_nuevo,
                    id_usuario_responsable, comentario, es_visible_denunciante
                  ) VALUES (
                    :id_denuncia, :id_estado_anterior, :id_estado_nuevo,
                    :id_usuario_responsable, :comentario, :es_visible_denunciante
                  )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $this->id_denuncia, PDO::PARAM_INT);
        $stmt->bindParam(':id_estado_anterior', $this->id_estado_anterior, PDO::PARAM_INT);
        $stmt->bindParam(':id_estado_nuevo', $this->id_estado_nuevo, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario_responsable', $this->id_usuario_responsable, PDO::PARAM_INT);
        $stmt->bindParam(':comentario', $this->comentario);
        $stmt->bindParam(':es_visible_denunciante', $this->es_visible_denunciante, PDO::PARAM_BOOL);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Obtener seguimiento por denuncia
     */
    public function obtenerPorDenuncia($id_denuncia, $visible_denunciante = null) {
        $whereClause = "WHERE s.id_denuncia = :id_denuncia";
        
        if ($visible_denunciante !== null) {
            $whereClause .= " AND s.es_visible_denunciante = :visible";
        }
        
        $query = "SELECT s.*, 
                         ea.nombre_estado as estado_anterior_nombre,
                         en.nombre_estado as estado_nuevo_nombre,
                         en.color as estado_nuevo_color,
                         CONCAT(u.nombres, ' ', u.apellidos) as responsable_nombre,
                         r.nombre_rol as responsable_rol
                  FROM seguimiento_denuncias s
                  LEFT JOIN estados_denuncia ea ON s.id_estado_anterior = ea.id_estado_denuncia
                  LEFT JOIN estados_denuncia en ON s.id_estado_nuevo = en.id_estado_denuncia
                  LEFT JOIN usuarios u ON s.id_usuario_responsable = u.id_usuario
                  LEFT JOIN roles r ON u.id_rol = r.id_rol
                  {$whereClause}
                  ORDER BY s.fecha_actualizacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        
        if ($visible_denunciante !== null) {
            $stmt->bindParam(':visible', $visible_denunciante, PDO::PARAM_BOOL);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener último seguimiento de una denuncia
     */
    public function obtenerUltimoPorDenuncia($id_denuncia) {
        $query = "SELECT s.*, 
                         en.nombre_estado as estado_nombre,
                         en.color as estado_color,
                         CONCAT(u.nombres, ' ', u.apellidos) as responsable_nombre
                  FROM seguimiento_denuncias s
                  LEFT JOIN estados_denuncia en ON s.id_estado_nuevo = en.id_estado_denuncia
                  LEFT JOIN usuarios u ON s.id_usuario_responsable = u.id_usuario
                  WHERE s.id_denuncia = :id_denuncia
                  ORDER BY s.fecha_actualizacion DESC
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>