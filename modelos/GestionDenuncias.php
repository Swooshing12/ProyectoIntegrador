<?php
// modelos/GestionDenuncias.php

require_once __DIR__ . "/../config/database.php";

class GestionDenuncias {
    public $conn;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Obtener denuncias con filtros
     */
    public function obtenerDenunciasConFiltros($filtros = []) {
        try {
            $where = ["1=1"];
            $params = [];
            
            // Filtro por estado
            if (!empty($filtros['estado'])) {
                $where[] = "d.id_estado_denuncia = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            // Filtro por categoría
            if (!empty($filtros['categoria'])) {
                $where[] = "d.id_categoria = :categoria";
                $params[':categoria'] = $filtros['categoria'];
            }
            
            // Filtro por tipo (AMBIENTAL/OBRAS_PUBLICAS)
            if (!empty($filtros['tipo'])) {
                $where[] = "c.tipo_principal = :tipo";
                $params[':tipo'] = $filtros['tipo'];
            }
            
            // Filtro por fecha
            if (!empty($filtros['fecha_desde'])) {
                $where[] = "DATE(d.fecha_creacion) >= :fecha_desde";
                $params[':fecha_desde'] = $filtros['fecha_desde'];
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $where[] = "DATE(d.fecha_creacion) <= :fecha_hasta";
                $params[':fecha_hasta'] = $filtros['fecha_hasta'];
            }
            
            // Filtro por institución (para responsables institucionales)
            if (!empty($filtros['institucion'])) {
                $where[] = "d.id_institucion_asignada = :institucion";
                $params[':institucion'] = $filtros['institucion'];
            }
            
            // Filtro por gravedad
            if (!empty($filtros['gravedad'])) {
                $where[] = "d.gravedad = :gravedad";
                $params[':gravedad'] = $filtros['gravedad'];
            }
            
            $whereClause = implode(" AND ", $where);
            
            $query = "SELECT 
                        d.*,
                        c.nombre_categoria,
                        c.tipo_principal,
                        c.icono as categoria_icono,
                        e.nombre_estado,
                        e.descripcion as estado_descripcion,
                        e.color as estado_color,
                        CONCAT(u.nombres, ' ', u.apellidos) as denunciante_nombre,
                        u.correo as denunciante_correo,
                        i.nombre_institucion,
                        i.siglas as institucion_siglas,
                        (SELECT COUNT(*) FROM evidencias_denuncia ev WHERE ev.id_denuncia = d.id_denuncia) as total_evidencias,
                        (SELECT COUNT(*) FROM seguimiento_denuncias s WHERE s.id_denuncia = d.id_denuncia) as total_seguimientos
                      FROM denuncias d
                      LEFT JOIN categorias_denuncia c ON d.id_categoria = c.id_categoria
                      LEFT JOIN estados_denuncia e ON d.id_estado_denuncia = e.id_estado_denuncia
                      LEFT JOIN usuarios u ON d.id_usuario_denunciante = u.id_usuario
                      LEFT JOIN instituciones_responsables i ON d.id_institucion_asignada = i.id_institucion
                      WHERE {$whereClause}
                      ORDER BY d.fecha_creacion DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo denuncias: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de denuncias
     */
    public function obtenerEstadisticas() {
        try {
            $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN d.id_estado_denuncia = 1 THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN d.id_estado_denuncia = 2 THEN 1 ELSE 0 END) as en_revision,
                        SUM(CASE WHEN d.id_estado_denuncia = 3 THEN 1 ELSE 0 END) as en_proceso,
                        SUM(CASE WHEN d.id_estado_denuncia = 4 THEN 1 ELSE 0 END) as resueltas,
                        SUM(CASE WHEN d.id_estado_denuncia = 5 THEN 1 ELSE 0 END) as cerradas,
                        SUM(CASE WHEN d.id_estado_denuncia = 6 THEN 1 ELSE 0 END) as rechazadas,
                        SUM(CASE WHEN c.tipo_principal = 'AMBIENTAL' THEN 1 ELSE 0 END) as ambientales,
                        SUM(CASE WHEN c.tipo_principal = 'OBRAS_PUBLICAS' THEN 1 ELSE 0 END) as obras_publicas
                      FROM denuncias d
                      LEFT JOIN categorias_denuncia c ON d.id_categoria = c.id_categoria";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo estadísticas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cambiar estado de denuncia
     */
    public function cambiarEstado($id_denuncia, $nuevo_estado, $comentario, $id_usuario_responsable) {
        try {
            $this->conn->beginTransaction();
            
            // Obtener estado actual
            $query = "SELECT id_estado_denuncia FROM denuncias WHERE id_denuncia = :id_denuncia";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
            $stmt->execute();
            $estado_actual = $stmt->fetchColumn();
            
            // Actualizar estado en denuncias
            $query = "UPDATE denuncias 
                      SET id_estado_denuncia = :nuevo_estado,
                          fecha_actualizacion = CURRENT_TIMESTAMP
                      WHERE id_denuncia = :id_denuncia";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
            $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
            $stmt->execute();
            
            // Crear registro de seguimiento
            $query = "INSERT INTO seguimiento_denuncias 
                      (id_denuncia, id_estado_anterior, id_estado_nuevo, id_usuario_responsable, comentario, es_visible_denunciante)
                      VALUES 
                      (:id_denuncia, :estado_anterior, :nuevo_estado, :usuario_responsable, :comentario, 1)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
            $stmt->bindParam(':estado_anterior', $estado_actual, PDO::PARAM_INT);
            $stmt->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_responsable', $id_usuario_responsable, PDO::PARAM_INT);
            $stmt->bindParam(':comentario', $comentario);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollback();
            error_log("Error cambiando estado: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Asignar denuncia a institución
     */
    /**
 * Asignar denuncia a institución (CORREGIDO)
 */
public function asignarInstitucion($id_denuncia, $id_institucion, $id_supervisor, $comentario, $prioridad = 'MEDIA') {
    try {
        $this->conn->beginTransaction();
        
        // 1. Actualizar denuncia con institución asignada
        $query = "UPDATE denuncias 
                  SET id_institucion_asignada = :id_institucion,
                      fecha_actualizacion = CURRENT_TIMESTAMP
                  WHERE id_denuncia = :id_denuncia";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        $stmt->execute();
        
        // 2. Crear registro de asignación
        $query = "INSERT INTO asignaciones_denuncia 
                  (id_denuncia, id_usuario_supervisor, id_institucion_asignada, comentario_asignacion, prioridad, estado_asignacion)
                  VALUES 
                  (:id_denuncia, :supervisor, :institucion, :comentario, :prioridad, 'ACTIVO')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        $stmt->bindParam(':supervisor', $id_supervisor, PDO::PARAM_INT);
        $stmt->bindParam(':institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->bindParam(':comentario', $comentario);
        $stmt->bindParam(':prioridad', $prioridad);
        $stmt->execute();
        
        // 3. Cambiar estado a "En Revisión" SIN nueva transacción
        $this->cambiarEstadoSinTransaccion($id_denuncia, 2, $comentario ?: 'Denuncia asignada para revisión', $id_supervisor);
        
        $this->conn->commit();
        return true;
    } catch (PDOException $e) {
        $this->conn->rollback();
        error_log("Error asignando institución: " . $e->getMessage());
        return false;
    }
}

/**
 * Cambiar estado SIN transacción (para uso interno)
 */
private function cambiarEstadoSinTransaccion($id_denuncia, $nuevo_estado, $comentario, $id_usuario_responsable) {
    // Obtener estado actual
    $query = "SELECT id_estado_denuncia FROM denuncias WHERE id_denuncia = :id_denuncia";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
    $stmt->execute();
    $estado_actual = $stmt->fetchColumn();
    
    // Actualizar estado en denuncias
    $query = "UPDATE denuncias 
              SET id_estado_denuncia = :nuevo_estado,
                  fecha_actualizacion = CURRENT_TIMESTAMP
              WHERE id_denuncia = :id_denuncia";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
    $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
    $stmt->execute();
    
    // Crear registro de seguimiento
    $query = "INSERT INTO seguimiento_denuncias 
              (id_denuncia, id_estado_anterior, id_estado_nuevo, id_usuario_responsable, comentario, es_visible_denunciante)
              VALUES 
              (:id_denuncia, :estado_anterior, :nuevo_estado, :usuario_responsable, :comentario, 1)";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
    $stmt->bindParam(':estado_anterior', $estado_actual, PDO::PARAM_INT);
    $stmt->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_responsable', $id_usuario_responsable, PDO::PARAM_INT);
    $stmt->bindParam(':comentario', $comentario);
    $stmt->execute();
}
    /**
     * Obtener todas las categorías
     */
    public function obtenerCategorias() {
        try {
            $query = "SELECT * FROM categorias_denuncia ORDER BY tipo_principal, nombre_categoria";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo categorías: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener todos los estados
     */
    public function obtenerEstados() {
        try {
            $query = "SELECT * FROM estados_denuncia ORDER BY id_estado_denuncia";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo estados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener todas las instituciones
     */
    public function obtenerInstituciones() {
        try {
            $query = "SELECT * FROM instituciones_responsables WHERE activo = 1 ORDER BY nombre_institucion";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo instituciones: " . $e->getMessage());
            return [];
        }
    }
}
?>