<?php
// modelos/CategoriasDenuncia.php

require_once __DIR__ . "/../config/database.php";

class CategoriasDenuncia {
    public $conn;
    
    public $id_categoria;
    public $nombre_categoria;
    public $descripcion;
    public $tipo_principal;
    public $icono;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Obtener todas las categorías
     */
    public function obtenerTodas() {
        $query = "SELECT id_categoria, nombre_categoria, descripcion, tipo_principal, icono 
                  FROM categorias_denuncia 
                  ORDER BY tipo_principal, nombre_categoria ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener categorías por tipo
     */
    public function obtenerPorTipo($tipo) {
        $query = "SELECT id_categoria, nombre_categoria, descripcion, icono 
                  FROM categorias_denuncia 
                  WHERE tipo_principal = :tipo 
                  ORDER BY nombre_categoria ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener categoría por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM categorias_denuncia WHERE id_categoria = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener categorías ambientales
     */
    public function obtenerAmbientales() {
        return $this->obtenerPorTipo('AMBIENTAL');
    }
    
    /**
     * Obtener categorías de obras públicas
     */
    public function obtenerObrasPublicas() {
        return $this->obtenerPorTipo('OBRAS_PUBLICAS');
    }
}
?>