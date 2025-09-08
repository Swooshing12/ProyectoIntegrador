<?php
// modelos/UsuariosInstituciones.php

require_once __DIR__ . '/../config/database.php';

class UsuariosInstituciones {
    private $conn;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Obtener todos los usuarios con rol 77
     */
    public function obtenerUsuariosResponsables() {
        $query = "SELECT u.id_usuario, u.nombres, u.apellidos, u.username, u.correo
                  FROM usuarios u 
                  WHERE u.id_rol = 77 AND u.id_estado = 1
                  ORDER BY u.nombres, u.apellidos";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todas las instituciones
     */
    public function obtenerInstituciones() {
        $query = "SELECT * FROM instituciones ORDER BY nombre_institucion";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener asignaciones existentes
     */
    public function obtenerAsignaciones() {
        $query = "SELECT ui.*, 
                         u.nombres, u.apellidos, u.username,
                         i.nombre_institucion, i.siglas
                  FROM usuarios_instituciones ui
                  JOIN usuarios u ON ui.id_usuario = u.id_usuario
                  JOIN instituciones i ON ui.id_institucion = i.id_institucion
                  ORDER BY i.nombre_institucion, ui.es_responsable_principal DESC, u.nombres";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear nueva asignación
     */
    public function crearAsignacion($id_usuario, $id_institucion, $es_responsable_principal = false, $comentarios = '') {
        try {
            // Verificar si ya existe la asignación
            $queryExiste = "SELECT COUNT(*) FROM usuarios_instituciones 
                           WHERE id_usuario = :id_usuario AND id_institucion = :id_institucion";
            $stmtExiste = $this->conn->prepare($queryExiste);
            $stmtExiste->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmtExiste->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmtExiste->execute();
            
            if ($stmtExiste->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'El usuario ya está asignado a esta institución'];
            }
            
            $query = "INSERT INTO usuarios_instituciones 
                      (id_usuario, id_institucion, es_responsable_principal, comentarios, estado_asignacion)
                      VALUES (:id_usuario, :id_institucion, :es_responsable_principal, :comentarios, 'ACTIVO')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
            $stmt->bindParam(':es_responsable_principal', $es_responsable_principal, PDO::PARAM_BOOL);
            $stmt->bindParam(':comentarios', $comentarios);
            
            $stmt->execute();
            
            return ['success' => true, 'message' => 'Usuario asignado correctamente'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Actualizar asignación
     */
    public function actualizarAsignacion($id, $es_responsable_principal, $comentarios, $estado_asignacion) {
        try {
            $query = "UPDATE usuarios_instituciones 
                      SET es_responsable_principal = :es_responsable_principal,
                          comentarios = :comentarios,
                          estado_asignacion = :estado_asignacion
                      WHERE id_usuario_institucion = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':es_responsable_principal', $es_responsable_principal, PDO::PARAM_BOOL);
            $stmt->bindParam(':comentarios', $comentarios);
            $stmt->bindParam(':estado_asignacion', $estado_asignacion);
            
            $stmt->execute();
            
            return ['success' => true, 'message' => 'Asignación actualizada correctamente'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar asignación
     */
    public function eliminarAsignacion($id) {
        try {
            $query = "DELETE FROM usuarios_instituciones WHERE id_usuario_institucion = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return ['success' => true, 'message' => 'Asignación eliminada correctamente'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener asignación por ID
     */
    public function obtenerAsignacionPorId($id) {
        $query = "SELECT ui.*, 
                         u.nombres, u.apellidos, u.username,
                         i.nombre_institucion, i.siglas
                  FROM usuarios_instituciones ui
                  JOIN usuarios u ON ui.id_usuario = u.id_usuario
                  JOIN instituciones i ON ui.id_institucion = i.id_institucion
                  WHERE ui.id_usuario_institucion = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>