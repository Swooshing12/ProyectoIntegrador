<?php
// modelos/EstadosDenuncia.php

require_once __DIR__ . "/../config/database.php";

class EstadosDenuncia {
    public $conn;
    
    public $id_estado_denuncia;
    public $nombre_estado;
    public $descripcion;
    public $color;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Obtener todos los estados
     */
    public function obtenerTodos() {
        $query = "SELECT id_estado_denuncia, nombre_estado, descripcion, color 
                  FROM estados_denuncia 
                  ORDER BY id_estado_denuncia ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estado por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM estados_denuncia WHERE id_estado_denuncia = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener nombre del estado
     */
    public function obtenerNombrePorId($id) {
        $query = "SELECT nombre_estado FROM estados_denuncia WHERE id_estado_denuncia = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['nombre_estado'] : 'Sin Estado';
    }
}
?>