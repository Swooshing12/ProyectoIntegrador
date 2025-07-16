<?php
// modelos/Notificaciones.php

require_once __DIR__ . "/../config/database.php";

class Notificaciones {
    public $conn;
    
    public $id_notificacion;
    public $id_usuario_destino;
    public $id_denuncia;
    public $tipo_notificacion;
    public $titulo;
    public $mensaje;
    public $leida;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Crear nueva notificación
     */
    public function crear() {
        $query = "INSERT INTO notificaciones (
                    id_usuario_destino, id_denuncia, tipo_notificacion,
                    titulo, mensaje
                  ) VALUES (
                    :id_usuario_destino, :id_denuncia, :tipo_notificacion,
                    :titulo, :mensaje
                  )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario_destino', $this->id_usuario_destino, PDO::PARAM_INT);
        $stmt->bindParam(':id_denuncia', $this->id_denuncia, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_notificacion', $this->tipo_notificacion);
        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':mensaje', $this->mensaje);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Obtener notificaciones por usuario
     */
    public function obtenerPorUsuario($id_usuario, $limite = 10) {
        $query = "SELECT n.*, d.numero_denuncia
                  FROM notificaciones n
                  LEFT JOIN denuncias d ON n.id_denuncia = d.id_denuncia
                  WHERE n.id_usuario_destino = :id_usuario
                  ORDER BY n.fecha_creacion DESC
                  LIMIT :limite";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida($id) {
        $query = "UPDATE notificaciones 
                  SET leida = 1, fecha_lectura = NOW() 
                  WHERE id_notificacion = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Contar notificaciones no leídas
     */
    public function contarNoLeidas($id_usuario) {
        $query = "SELECT COUNT(*) as total 
                  FROM notificaciones 
                  WHERE id_usuario_destino = :id_usuario AND leida = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }
    
    /**
     * Marcar todas como leídas para un usuario
     */
    public function marcarTodasLeidas($id_usuario) {
        $query = "UPDATE notificaciones 
                  SET leida = 1, fecha_lectura = NOW() 
                  WHERE id_usuario_destino = :id_usuario AND leida = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>