<?php
// modelos/PanelDenuncias.php

require_once __DIR__ . '/../config/database.php';

class PanelDenuncias {
    private $conn;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    /**
     * Obtener denuncias asignadas a las instituciones del usuario responsable
     */
    public function obtenerDenunciasUsuario($id_usuario, $filtros = []) {
        $query = "SELECT d.*, 
                         c.nombre_categoria, c.tipo_principal, c.icono,
                         ed.nombre_estado, ed.color, ed.descripcion as estado_descripcion,
                         ir.nombre_institucion, ir.siglas,
                         ad.prioridad, ad.comentario_asignacion, ad.fecha_asignacion,
                         u.nombres as denunciante_nombres, u.apellidos as denunciante_apellidos,
                         u.correo as denunciante_correo,
                         (SELECT COUNT(*) FROM evidencias_denuncia WHERE id_denuncia = d.id_denuncia) as total_evidencias
                  FROM denuncias d
                  JOIN asignaciones_denuncia ad ON d.id_denuncia = ad.id_denuncia
                  JOIN usuarios_instituciones ui ON ad.id_institucion_asignada = ui.id_institucion
                  JOIN categorias_denuncia c ON d.id_categoria = c.id_categoria
                  JOIN estados_denuncia ed ON d.id_estado_denuncia = ed.id_estado_denuncia
                  JOIN instituciones_responsables ir ON ad.id_institucion_asignada = ir.id_institucion
                  JOIN usuarios u ON d.id_usuario_denunciante = u.id_usuario
                  WHERE ui.id_usuario = :id_usuario 
                    AND ui.estado_asignacion = 'ACTIVO'
                    AND ad.estado_asignacion = 'ACTIVO'";
        
        // Aplicar filtros
        if (!empty($filtros['estado'])) {
            $query .= " AND d.id_estado_denuncia = :estado";
        }
        if (!empty($filtros['prioridad'])) {
            $query .= " AND ad.prioridad = :prioridad";
        }
        if (!empty($filtros['tipo'])) {
            $query .= " AND c.tipo_principal = :tipo";
        }
        if (!empty($filtros['busqueda'])) {
            $query .= " AND (d.numero_denuncia LIKE :busqueda OR d.titulo LIKE :busqueda OR d.descripcion LIKE :busqueda)";
        }
        
        $query .= " ORDER BY 
                      CASE ad.prioridad 
                        WHEN 'URGENTE' THEN 1
                        WHEN 'ALTA' THEN 2  
                        WHEN 'MEDIA' THEN 3
                        WHEN 'BAJA' THEN 4
                      END,
                      d.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        if (!empty($filtros['estado'])) {
            $stmt->bindParam(':estado', $filtros['estado'], PDO::PARAM_INT);
        }
        if (!empty($filtros['prioridad'])) {
            $stmt->bindParam(':prioridad', $filtros['prioridad']);
        }
        if (!empty($filtros['tipo'])) {
            $stmt->bindParam(':tipo', $filtros['tipo']);
        }
        if (!empty($filtros['busqueda'])) {
            $busqueda = '%' . $filtros['busqueda'] . '%';
            $stmt->bindParam(':busqueda', $busqueda);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener detalle completo de una denuncia
     */
    public function obtenerDetalleDenuncia($id_denuncia, $id_usuario) {
        $query = "SELECT d.*, 
                         c.nombre_categoria, c.tipo_principal, c.icono, c.descripcion as categoria_descripcion,
                         ed.nombre_estado, ed.color, ed.descripcion as estado_descripcion,
                         ir.nombre_institucion, ir.siglas, ir.contacto_email, ir.contacto_telefono,
                         ad.prioridad, ad.comentario_asignacion, ad.fecha_asignacion,
                         u.nombres as denunciante_nombres, u.apellidos as denunciante_apellidos,
                         u.correo as denunciante_correo, u.telefono_contacto as denunciante_telefono
                  FROM denuncias d
                  JOIN asignaciones_denuncia ad ON d.id_denuncia = ad.id_denuncia
                  JOIN usuarios_instituciones ui ON ad.id_institucion_asignada = ui.id_institucion
                  JOIN categorias_denuncia c ON d.id_categoria = c.id_categoria
                  JOIN estados_denuncia ed ON d.id_estado_denuncia = ed.id_estado_denuncia
                  JOIN instituciones_responsables ir ON ad.id_institucion_asignada = ir.id_institucion
                  JOIN usuarios u ON d.id_usuario_denunciante = u.id_usuario
                  WHERE d.id_denuncia = :id_denuncia
                    AND ui.id_usuario = :id_usuario 
                    AND ui.estado_asignacion = 'ACTIVO'
                    AND ad.estado_asignacion = 'ACTIVO'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cambiar estado de denuncia
     */
    public function cambiarEstadoDenuncia($id_denuncia, $nuevo_estado, $id_usuario_responsable, $comentario = '') {
        try {
            $this->conn->beginTransaction();
            
            // Obtener estado actual
            $queryActual = "SELECT id_estado_denuncia FROM denuncias WHERE id_denuncia = :id_denuncia";
            $stmtActual = $this->conn->prepare($queryActual);
            $stmtActual->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
            $stmtActual->execute();
            $estadoActual = $stmtActual->fetchColumn();
            
            if ($estadoActual == $nuevo_estado) {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'La denuncia ya se encuentra en ese estado'];
            }
            
            // Actualizar estado en tabla denuncias
            $queryUpdate = "UPDATE denuncias 
                           SET id_estado_denuncia = :nuevo_estado,
                               fecha_actualizacion = CURRENT_TIMESTAMP";
            
            // Si es resuelto o cerrado, agregar fecha de resolución
            if (in_array($nuevo_estado, [4, 5])) { // 4=Resuelto, 5=Cerrado
                $queryUpdate .= ", fecha_resolucion = CURRENT_TIMESTAMP";
            }
            
            $queryUpdate .= " WHERE id_denuncia = :id_denuncia";
            
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
            $stmtUpdate->execute();
            
            // Registrar en seguimiento
            $querySeguimiento = "INSERT INTO seguimiento_denuncias 
                               (id_denuncia, id_estado_anterior, id_estado_nuevo, id_usuario_responsable, comentario, es_visible_denunciante)
                               VALUES (:id_denuncia, :estado_anterior, :estado_nuevo, :id_usuario, :comentario, 1)";
            
            $stmtSeguimiento = $this->conn->prepare($querySeguimiento);
            $stmtSeguimiento->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
            $stmtSeguimiento->bindParam(':estado_anterior', $estadoActual, PDO::PARAM_INT);
            $stmtSeguimiento->bindParam(':estado_nuevo', $nuevo_estado, PDO::PARAM_INT);
            $stmtSeguimiento->bindParam(':id_usuario', $id_usuario_responsable, PDO::PARAM_INT);
            $stmtSeguimiento->bindParam(':comentario', $comentario);
            $stmtSeguimiento->execute();
            
            // Crear notificación para el denunciante
            $this->crearNotificacionCambioEstado($id_denuncia, $nuevo_estado, $comentario);
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Estado actualizado correctamente'];
            
        } catch (PDOException $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener todos los estados disponibles
     */
    public function obtenerEstados() {
        $query = "SELECT * FROM estados_denuncia ORDER BY id_estado_denuncia";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener evidencias de una denuncia
     */
    public function obtenerEvidencias($id_denuncia) {
        $query = "SELECT * FROM evidencias_denuncia 
                  WHERE id_denuncia = :id_denuncia 
                  ORDER BY fecha_subida DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener seguimiento de una denuncia
     */
    public function obtenerSeguimiento($id_denuncia) {
        $query = "SELECT s.*, 
                         ed_ant.nombre_estado as estado_anterior_nombre,
                         ed_new.nombre_estado as estado_nuevo_nombre,
                         ed_new.color as estado_nuevo_color,
                         u.nombres, u.apellidos
                  FROM seguimiento_denuncias s
                  LEFT JOIN estados_denuncia ed_ant ON s.id_estado_anterior = ed_ant.id_estado_denuncia
                  JOIN estados_denuncia ed_new ON s.id_estado_nuevo = ed_new.id_estado_denuncia
                  JOIN usuarios u ON s.id_usuario_responsable = u.id_usuario
                  WHERE s.id_denuncia = :id_denuncia
                  ORDER BY s.fecha_actualizacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear notificación de cambio de estado
     */
    private function crearNotificacionCambioEstado($id_denuncia, $nuevo_estado, $comentario) {
        // Obtener datos de la denuncia
        $queryDenuncia = "SELECT d.numero_denuncia, d.id_usuario_denunciante, ed.nombre_estado
                         FROM denuncias d
                         JOIN estados_denuncia ed ON d.id_estado_denuncia = ed.id_estado_denuncia
                         WHERE d.id_denuncia = :id_denuncia";
        
        $stmtDenuncia = $this->conn->prepare($queryDenuncia);
        $stmtDenuncia->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
        $stmtDenuncia->execute();
        $denuncia = $stmtDenuncia->fetch(PDO::FETCH_ASSOC);
        
        if ($denuncia) {
            $titulo = "Actualización de denuncia " . $denuncia['numero_denuncia'];
            $mensaje = "Su denuncia ha cambiado al estado: " . $denuncia['nombre_estado'];
            if (!empty($comentario)) {
                $mensaje .= "\n\nComentario: " . $comentario;
            }
            
            $queryNotif = "INSERT INTO notificaciones 
                          (id_usuario_destino, id_denuncia, tipo_notificacion, titulo, mensaje)
                          VALUES (:id_usuario, :id_denuncia, 'CAMBIO_ESTADO', :titulo, :mensaje)";
            
            $stmtNotif = $this->conn->prepare($queryNotif);
            $stmtNotif->bindParam(':id_usuario', $denuncia['id_usuario_denunciante'], PDO::PARAM_INT);
            $stmtNotif->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
            $stmtNotif->bindParam(':titulo', $titulo);
            $stmtNotif->bindParam(':mensaje', $mensaje);
            $stmtNotif->execute();
        }
    }
    
    /**
     * Obtener estadísticas del responsable
     */
    public function obtenerEstadisticas($id_usuario) {
        $query = "SELECT 
                    COUNT(*) as total_denuncias,
                    SUM(CASE WHEN ed.id_estado_denuncia = 1 THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN ed.id_estado_denuncia = 2 THEN 1 ELSE 0 END) as en_revision,
                    SUM(CASE WHEN ed.id_estado_denuncia = 3 THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN ed.id_estado_denuncia = 4 THEN 1 ELSE 0 END) as resueltas,
                    SUM(CASE WHEN ed.id_estado_denuncia = 5 THEN 1 ELSE 0 END) as cerradas,
                    SUM(CASE WHEN ad.prioridad = 'URGENTE' THEN 1 ELSE 0 END) as urgentes,
                    SUM(CASE WHEN ad.prioridad = 'ALTA' THEN 1 ELSE 0 END) as alta_prioridad
                  FROM denuncias d
                  JOIN asignaciones_denuncia ad ON d.id_denuncia = ad.id_denuncia
                  JOIN usuarios_instituciones ui ON ad.id_institucion_asignada = ui.id_institucion
                  JOIN estados_denuncia ed ON d.id_estado_denuncia = ed.id_estado_denuncia
                  WHERE ui.id_usuario = :id_usuario 
                    AND ui.estado_asignacion = 'ACTIVO'
                    AND ad.estado_asignacion = 'ACTIVO'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
 * Subir evidencia desde el seguimiento
 */
public function subirEvidenciaSeguimiento($id_denuncia, $archivo, $id_usuario, $descripcion = '') {
    try {
        // Validar que el usuario tenga acceso a esta denuncia
        $tieneAcceso = $this->verificarAccesoDenuncia($id_denuncia, $id_usuario);
        if (!$tieneAcceso) {
            return ['success' => false, 'message' => 'No tienes acceso a esta denuncia'];
        }
        
        // Validar archivo
        $validacion = $this->validarArchivo($archivo);
        if (!$validacion['success']) {
            return $validacion;
        }
        
        // Generar nombre único
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'evidencia_' . $id_denuncia . '_' . time() . '_' . uniqid() . '.' . $extension;
        
        // Crear directorio si no existe
        $directorioDestino = __DIR__ . '/../uploads/evidencias/';
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }
        
        $rutaCompleta = $directorioDestino . $nombreArchivo;
        $rutaRelativa = 'uploads/evidencias/' . $nombreArchivo;
        
        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            // Insertar en base de datos
            $query = "INSERT INTO evidencias_denuncia 
                      (id_denuncia, tipo_evidencia, nombre_archivo, ruta_archivo, tamaño_archivo, descripcion, subido_por)
                      VALUES (:id_denuncia, :tipo_evidencia, :nombre_archivo, :ruta_archivo, :tamaño_archivo, :descripcion, :subido_por)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
            $stmt->bindParam(':tipo_evidencia', $validacion['tipo']);
            $stmt->bindParam(':nombre_archivo', $archivo['name']);
            $stmt->bindParam(':ruta_archivo', $rutaRelativa);
            $stmt->bindParam(':tamaño_archivo', $archivo['size'], PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':subido_por', $id_usuario, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return [
                'success' => true, 
                'message' => 'Evidencia subida correctamente',
                'archivo' => $nombreArchivo,
                'ruta' => $rutaRelativa
            ];
        } else {
            return ['success' => false, 'message' => 'Error al subir el archivo'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

/**
 * Validar archivo subido
 */
private function validarArchivo($archivo) {
    // Validar errores de subida
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error en la subida del archivo'];
    }
    
    // Validar tamaño (máximo 4MB)
    $maxSize = 4 * 1024 * 1024; // 4MB
    if ($archivo['size'] > $maxSize) {
        return ['success' => false, 'message' => 'El archivo es demasiado grande (máximo 4MB)'];
    }
    
    // Validar tipo de archivo
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $tiposPermitidos = [
        'jpg' => 'FOTO',
        'jpeg' => 'FOTO', 
        'png' => 'FOTO',
        'gif' => 'FOTO',
        'mp4' => 'VIDEO',
        'avi' => 'VIDEO',
        'mov' => 'VIDEO',
        'pdf' => 'DOCUMENTO',
        'doc' => 'DOCUMENTO',
        'docx' => 'DOCUMENTO',
        'txt' => 'DOCUMENTO',
        'mp3' => 'AUDIO',
        'wav' => 'AUDIO'
    ];
    
    if (!isset($tiposPermitidos[$extension])) {
        return ['success' => false, 'message' => 'Tipo de archivo no permitido'];
    }
    
    return [
        'success' => true,
        'tipo' => $tiposPermitidos[$extension]
    ];
}

/**
 * Verificar que el usuario tenga acceso a la denuncia
 */
private function verificarAccesoDenuncia($id_denuncia, $id_usuario) {
    $query = "SELECT COUNT(*) FROM denuncias d
              JOIN asignaciones_denuncia ad ON d.id_denuncia = ad.id_denuncia
              JOIN usuarios_instituciones ui ON ad.id_institucion_asignada = ui.id_institucion
              WHERE d.id_denuncia = :id_denuncia 
                AND ui.id_usuario = :id_usuario 
                AND ui.estado_asignacion = 'ACTIVO'
                AND ad.estado_asignacion = 'ACTIVO'";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id_denuncia', $id_denuncia, PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchColumn() > 0;
}

/**
 * Cambiar estado con evidencias
 */
public function cambiarEstadoConEvidencias($id_denuncia, $nuevo_estado, $id_usuario_responsable, $comentario = '', $archivos = []) {
    try {
        $this->conn->beginTransaction();
        
        // Cambiar estado normal
        $resultado = $this->cambiarEstadoDenuncia($id_denuncia, $nuevo_estado, $id_usuario_responsable, $comentario);
        
        if (!$resultado['success']) {
            $this->conn->rollback();
            return $resultado;
        }
        
        // Subir archivos si los hay
        $archivosSubidos = [];
        if (!empty($archivos)) {
            foreach ($archivos as $archivo) {
                if ($archivo['error'] === UPLOAD_ERR_OK) {
                    $resultadoArchivo = $this->subirEvidenciaSeguimiento($id_denuncia, $archivo, $id_usuario_responsable, 'Evidencia del seguimiento');
                    if ($resultadoArchivo['success']) {
                        $archivosSubidos[] = $resultadoArchivo['archivo'];
                    }
                }
            }
        }
        
        $this->conn->commit();
        
        $mensaje = $resultado['message'];
        if (!empty($archivosSubidos)) {
            $mensaje .= '. ' . count($archivosSubidos) . ' archivo(s) subido(s) correctamente.';
        }
        
        return ['success' => true, 'message' => $mensaje];
        
    } catch (Exception $e) {
        $this->conn->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}
}
?>