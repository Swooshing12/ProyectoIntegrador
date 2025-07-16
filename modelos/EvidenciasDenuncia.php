<?php
// modelos/EvidenciasDenuncia.php

require_once __DIR__ . "/../config/database.php";

class EvidenciasDenuncia {
    public $conn;
    
    public $id_evidencia;
    public $id_denuncia;
    public $tipo_evidencia;
    public $nombre_archivo;
    public $ruta_archivo;
    public $tamaño_archivo;
    public $descripcion;
    public $subido_por;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Crear nueva evidencia
     */
    public function crear() {
        $query = "INSERT INTO evidencias_denuncia (
                    id_denuncia, tipo_evidencia, nombre_archivo, ruta_archivo,
                    tamaño_archivo, descripcion, subido_por
                  ) VALUES (
                    :id_denuncia, :tipo_evidencia, :nombre_archivo, :ruta_archivo,
                    :tamaño_archivo, :descripcion, :subido_por
                  )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $this->id_denuncia, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_evidencia', $this->tipo_evidencia);
        $stmt->bindParam(':nombre_archivo', $this->nombre_archivo);
        $stmt->bindParam(':ruta_archivo', $this->ruta_archivo);
        $stmt->bindParam(':tamaño_archivo', $this->tamaño_archivo, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':subido_por', $this->subido_por, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Obtener evidencias por denuncia
     */
    public function obtenerPorDenuncia($id_denuncia) {
        $query = "SELECT e.*, 
                         CONCAT(u.nombres, ' ', u.apellidos) as subido_por_nombre
                  FROM evidencias_denuncia e
                  LEFT JOIN usuarios u ON e.subido_por = u.id_usuario
                  WHERE e.id_denuncia = :id_denuncia
                  ORDER BY e.fecha_subida DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Eliminar evidencia
     */
    public function eliminar($id) {
        $query = "DELETE FROM evidencias_denuncia WHERE id_evidencia = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>