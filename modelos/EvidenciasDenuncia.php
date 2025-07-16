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
    try {
        $query = "INSERT INTO evidencias_denuncia (
                    id_denuncia, tipo_evidencia, nombre_archivo, ruta_archivo,
                    tamaño_archivo, descripcion, subido_por
                  ) VALUES (
                    :id_denuncia, :tipo_evidencia, :nombre_archivo, :ruta_archivo,
                    :tamano_archivo, :descripcion, :subido_por
                  )";
        
        $stmt = $this->conn->prepare($query);
        
        // ✅ VERIFICAR que todas las propiedades estén definidas
        error_log("🔍 DEBUG Evidencia - ID Denuncia: " . $this->id_denuncia);
        error_log("🔍 DEBUG Evidencia - Tipo: " . $this->tipo_evidencia);
        error_log("🔍 DEBUG Evidencia - Nombre: " . $this->nombre_archivo);
        error_log("🔍 DEBUG Evidencia - Ruta: " . $this->ruta_archivo);
        error_log("🔍 DEBUG Evidencia - Tamaño: " . $this->tamaño_archivo);
        error_log("🔍 DEBUG Evidencia - Subido por: " . $this->subido_por);
        
        $stmt->bindParam(':id_denuncia', $this->id_denuncia, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_evidencia', $this->tipo_evidencia);
        $stmt->bindParam(':nombre_archivo', $this->nombre_archivo);
        $stmt->bindParam(':ruta_archivo', $this->ruta_archivo);
        $stmt->bindParam(':tamano_archivo', $this->tamaño_archivo, PDO::PARAM_INT); // ✅ SIN TILDE
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':subido_por', $this->subido_por, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $evidencia_id = $this->conn->lastInsertId();
            error_log("✅ Evidencia guardada con ID: " . $evidencia_id);
            return $evidencia_id;
        }
        
        error_log("❌ Error ejecutando INSERT de evidencia");
        return false;
        
    } catch (PDOException $e) {
        error_log("❌ Error SQL en evidencias: " . $e->getMessage());
        error_log("❌ Query: " . ($query ?? 'Query no definida'));
        throw $e;
    }
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