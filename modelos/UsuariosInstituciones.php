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
     * Obtener todas las instituciones ✅ CORREGIDO
     */
    public function obtenerInstituciones() {
        $query = "SELECT id_institucion, nombre_institucion, siglas, tipo_institucion, 
                         contacto_email, contacto_telefono, responsable_nombre, responsable_cargo
                  FROM instituciones_responsables 
                  WHERE activo = 1
                  ORDER BY nombre_institucion";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener asignaciones existentes ✅ CORREGIDO
     */
    public function obtenerAsignaciones() {
        $query = "SELECT ui.*, 
                         u.nombres, u.apellidos, u.username,
                         ir.nombre_institucion, ir.siglas
                  FROM usuarios_instituciones ui
                  JOIN usuarios u ON ui.id_usuario = u.id_usuario
                  JOIN instituciones_responsables ir ON ui.id_institucion = ir.id_institucion
                  ORDER BY ir.nombre_institucion, ui.es_responsable_principal DESC, u.nombres";
        
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
     * Obtener asignación por ID ✅ CORREGIDO
     */
    public function obtenerAsignacionPorId($id) {
        $query = "SELECT ui.*, 
                         u.nombres, u.apellidos, u.username,
                         ir.nombre_institucion, ir.siglas
                  FROM usuarios_instituciones ui
                  JOIN usuarios u ON ui.id_usuario = u.id_usuario
                  JOIN instituciones_responsables ir ON ui.id_institucion = ir.id_institucion
                  WHERE ui.id_usuario_institucion = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * ✅ NUEVO: Obtener usuarios por institución
     */
    public function obtenerUsuariosPorInstitucion($id_institucion) {
        $query = "SELECT ui.*, 
                         u.nombres, u.apellidos, u.username, u.correo,
                         ir.nombre_institucion, ir.siglas
                  FROM usuarios_instituciones ui
                  JOIN usuarios u ON ui.id_usuario = u.id_usuario
                  JOIN instituciones_responsables ir ON ui.id_institucion = ir.id_institucion
                  WHERE ui.id_institucion = :id_institucion AND ui.estado_asignacion = 'ACTIVO'
                  ORDER BY ui.es_responsable_principal DESC, u.nombres";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_institucion', $id_institucion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * ✅ NUEVO: Obtener instituciones por usuario
     */
    public function obtenerInstitucionesPorUsuario($id_usuario) {
        $query = "SELECT ui.*, 
                         ir.nombre_institucion, ir.siglas, ir.tipo_institucion,
                         ir.contacto_email, ir.contacto_telefono
                  FROM usuarios_instituciones ui
                  JOIN instituciones_responsables ir ON ui.id_institucion = ir.id_institucion
                  WHERE ui.id_usuario = :id_usuario AND ui.estado_asignacion = 'ACTIVO'
                  ORDER BY ui.es_responsable_principal DESC, ir.nombre_institucion";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>