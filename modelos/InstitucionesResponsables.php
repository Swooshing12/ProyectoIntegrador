<?php
// modelos/InstitucionesResponsables.php

require_once __DIR__ . "/../config/database.php";

class InstitucionesResponsables {
    public $conn;
    
    public $id_institucion;
    public $nombre_institucion;
    public $siglas;
    public $tipo_institucion;
    public $contacto_email;
    public $contacto_telefono;
    public $responsable_nombre;
    public $responsable_cargo;
    public $activo;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Obtener todas las instituciones activas
     */
    public function obtenerTodas() {
        $query = "SELECT id_institucion, nombre_institucion, siglas, tipo_institucion,
                         contacto_email, contacto_telefono, responsable_nombre, responsable_cargo
                  FROM instituciones_responsables 
                  WHERE activo = 1
                  ORDER BY nombre_institucion ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener institución por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM instituciones_responsables WHERE id_institucion = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener instituciones por tipo
     */
    public function obtenerPorTipo($tipo) {
        $query = "SELECT id_institucion, nombre_institucion, siglas, responsable_nombre
                  FROM instituciones_responsables 
                  WHERE tipo_institucion = :tipo AND activo = 1
                  ORDER BY nombre_institucion ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener instituciones para una categoría específica
     */
    public function obtenerPorCategoria($id_categoria) {
        $query = "SELECT i.id_institucion, i.nombre_institucion, i.siglas, ic.es_principal
                  FROM instituciones_responsables i
                  INNER JOIN institucion_categorias ic ON i.id_institucion = ic.id_institucion
                  WHERE ic.id_categoria = :id_categoria AND i.activo = 1 AND ic.activo = 1
                  ORDER BY ic.es_principal DESC, i.nombre_institucion ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener nombre de institución por ID
     */
    public function obtenerNombrePorId($id) {
        $query = "SELECT nombre_institucion FROM instituciones_responsables WHERE id_institucion = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['nombre_institucion'] : 'Sin Institución';
    }
}
?>