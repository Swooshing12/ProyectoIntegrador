<?php
// modelos/ConsultaDenuncia.php

require_once __DIR__ . "/../config/database.php";

class ConsultaDenuncia {
    public $conn;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Buscar denuncia por número de denuncia
     */
    public function buscarPorNumero($numero_denuncia) {
        try {
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
                        i.siglas as institucion_siglas
                      FROM denuncias d
                      LEFT JOIN categorias_denuncia c ON d.id_categoria = c.id_categoria
                      LEFT JOIN estados_denuncia e ON d.id_estado_denuncia = e.id_estado_denuncia
                      LEFT JOIN usuarios u ON d.id_usuario_denunciante = u.id_usuario
                      LEFT JOIN instituciones_responsables i ON d.id_institucion_asignada = i.id_institucion
                      WHERE d.numero_denuncia = :numero_denuncia";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':numero_denuncia', $numero_denuncia);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error buscando denuncia: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener seguimiento de la denuncia
     */
    public function obtenerSeguimiento($id_denuncia) {
        try {
            $query = "SELECT 
                        s.*,
                        ea.nombre_estado as estado_anterior_nombre,
                        ea.color as estado_anterior_color,
                        en.nombre_estado as estado_nuevo_nombre,
                        en.color as estado_nuevo_color,
                        CONCAT(u.nombres, ' ', u.apellidos) as responsable_nombre,
                        r.nombre_rol as responsable_rol
                      FROM seguimiento_denuncias s
                      LEFT JOIN estados_denuncia ea ON s.id_estado_anterior = ea.id_estado_denuncia
                      LEFT JOIN estados_denuncia en ON s.id_estado_nuevo = en.id_estado_denuncia
                      LEFT JOIN usuarios u ON s.id_usuario_responsable = u.id_usuario
                      LEFT JOIN roles r ON u.id_rol = r.id_rol
                      WHERE s.id_denuncia = :id_denuncia 
                      AND s.es_visible_denunciante = 1
                      ORDER BY s.fecha_actualizacion ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo seguimiento: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener evidencias de la denuncia
     */
    public function obtenerEvidencias($id_denuncia) {
        try {
            $query = "SELECT 
                        ev.*,
                        CONCAT(u.nombres, ' ', u.apellidos) as subido_por_nombre
                      FROM evidencias_denuncia ev
                      LEFT JOIN usuarios u ON ev.subido_por = u.id_usuario
                      WHERE ev.id_denuncia = :id_denuncia
                      ORDER BY ev.fecha_subida ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo evidencias: " . $e->getMessage());
            return [];
        }
    }
}
?>