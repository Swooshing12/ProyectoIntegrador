<?php
require_once __DIR__ . "/../../modelos/Denuncias.php";
require_once __DIR__ . "/../../modelos/CategoriasDenuncia.php";
require_once __DIR__ . "/../../modelos/Usuario.php";
require_once __DIR__ . "/../../modelos/EvidenciasDenuncia.php";
require_once __DIR__ . "/../../config/MailService.php";

class FormatoDenunciasController {
    private $denunciasModel;
    private $categoriasModel;
    private $usuarioModel;
    private $evidenciasModel;
    private $mailService;
    private $debug = false;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->denunciasModel = new Denuncias();
        $this->categoriasModel = new CategoriasDenuncia();
        $this->usuarioModel = new Usuario();
        $this->evidenciasModel = new EvidenciasDenuncia();
        $this->mailService = new MailService();
    }

    public function manejarSolicitud() {
        $action = $_GET['action'] ?? $_POST['action'] ?? 'index';
        
        try {
            switch ($action) {
                case 'crear':
                    $this->crearDenuncia();
                    break;
                case 'obtenerCategorias':
                    $this->obtenerCategorias();
                    break;
                case 'guardarBorrador':
                    $this->guardarBorrador();
                    break;
                case 'subirEvidencia':
                    $this->subirEvidencia();
                    break;
                case 'index':
                default:
                    $this->index();
                    break;
            }
        } catch (Exception $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 🔹 Mostrar formulario de denuncias
     */
    public function index() {
        try {
            // Obtener categorías para el formulario
            $categorias = $this->categoriasModel->obtenerTodas();
            
            // Preparar datos para la vista
            $data = [
                'categorias' => $categorias,
                'titulo' => 'Formulario de Denuncias - EcoReport'
            ];
            
            // Incluir la vista
            include __DIR__ . '/../../vistas/denuncias/formatodenuncias.php';
            
        } catch (Exception $e) {
            $this->mostrarError('Error al cargar el formulario', $e->getMessage());
        }
    }

    /**
     * 🔹 Crear nueva denuncia
     */
    private function crearDenuncia() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
            return;
        }

        try {
            // Validar datos requeridos
            $errores = $this->validarDatosDenuncia($_POST);
            if (!empty($errores)) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Datos incompletos o inválidos',
                    'errores' => $errores
                ]);
                return;
            }

            // 1. Crear o obtener usuario denunciante
            $id_usuario_denunciante = $this->procesarUsuarioDenunciante($_POST);
            if (!$id_usuario_denunciante) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Error al procesar los datos del denunciante'
                ]);
                return;
            }

                            // ✅ CORREGIR: Asignar datos al modelo de denuncia
                $this->denunciasModel->titulo = $_POST['titulo'] ?? 'Denuncia Ambiental ' . date('Y-m-d H:i');
                $this->denunciasModel->descripcion = $_POST['narracion_hechos'];
                $this->denunciasModel->id_categoria = (int)$_POST['id_categoria'];
                $this->denunciasModel->id_usuario_denunciante = $id_usuario_denunciante;
                $this->denunciasModel->id_estado_denuncia = 1; // ✅ AÑADIR ESTADO POR DEFECTO
                $this->denunciasModel->provincia = $_POST['provincia'];
                $this->denunciasModel->canton = $_POST['canton'];
                $this->denunciasModel->parroquia = $_POST['parroquia'] ?? '';
                $this->denunciasModel->direccion_especifica = $_POST['direccion_especifica'] ?? '';
                $this->denunciasModel->fecha_ocurrencia = $_POST['fecha_ocurrencia'] ?? date('Y-m-d');
                $this->denunciasModel->gravedad = $_POST['gravedad'] ?? 'MEDIA';
                $this->denunciasModel->servidor_municipal = $_POST['servidor_municipal'] ?? '';
                $this->denunciasModel->entidad_municipal = $_POST['entidad_municipal'] ?? '';
                $this->denunciasModel->informacion_adicional_denunciado = $_POST['informacion_adicional'] ?? '';
                $this->denunciasModel->requiere_atencion_prioritaria = ($_POST['atencion_prioritaria'] ?? 'no') === 'si';
                $this->denunciasModel->acepta_politica_privacidad = isset($_POST['acepta_politica']);

                // ✅ LOG PARA DEBUG
                error_log("🔍 DEBUG - Datos de denuncia:");
                error_log("Título: " . $this->denunciasModel->titulo);
                error_log("Descripción: " . substr($this->denunciasModel->descripcion, 0, 50) . '...');
                error_log("Categoría: " . $this->denunciasModel->id_categoria);
                error_log("Usuario: " . $this->denunciasModel->id_usuario_denunciante);
                error_log("Estado: " . $this->denunciasModel->id_estado_denuncia);

            // 3. Crear la denuncia
            $id_denuncia = $this->denunciasModel->crear();
            
            if (!$id_denuncia) {
                $this->responderJSON([
                    'success' => false,
                    'message' => 'Error al crear la denuncia'
                ]);
                return;
            }

            // 4. Procesar evidencias si existen
                $evidencias_procesadas = 0;
                try {
                    // ✅ DEBUG: Ver qué llega en $_FILES
                    error_log("🔍 DEBUG \$_FILES: " . print_r($_FILES, true));
                    
                    if (isset($_FILES['evidencias']) && !empty($_FILES['evidencias']['name'][0])) {
                        error_log("📁 Evidencias detectadas - Procesando array");
                    $evidencias_procesadas = $this->procesarEvidencias($id_denuncia, $_FILES['evidencias'], $id_usuario_denunciante);
                        error_log("📁 Evidencias procesadas exitosamente: $evidencias_procesadas");
                    } else {
                        error_log("📁 No hay evidencias para procesar");
                    }
                } catch (Exception $e) {
                    error_log("❌ Error procesando evidencias: " . $e->getMessage());
                    // No fallar la denuncia por error de evidencias
                }

                            // 5. Generar número de denuncia
            $numero_denuncia = $this->generarNumeroDenuncia($id_denuncia);
            $this->actualizarNumeroDenuncia($id_denuncia, $numero_denuncia);

            // 6. Enviar notificación por correo
            $usuario = $this->usuarioModel->obtenerPorId($id_usuario_denunciante);
                // En el método crearDenuncia(), cambiar:
                $envio_exitoso = $this->enviarNotificacionDenuncia($usuario, $numero_denuncia, [
                    'categoria' => $datos['categoria'] ?? '',
                    'provincia' => $_POST['provincia'],
                    'canton' => $_POST['canton'],
                    'gravedad' => $_POST['gravedad'] ?? 'MEDIA'
                ]);
                            // 7. Respuesta exitosa
            $this->responderJSON([
                'success' => true,
                'message' => 'Denuncia creada exitosamente',
                'numero_denuncia' => $numero_denuncia,
                'id_denuncia' => $id_denuncia,
                'evidencias_procesadas' => $evidencias_procesadas,
                'email_enviado' => $envio_exitoso
            ]);

        } catch (Exception $e) {
            error_log("Error creando denuncia: " . $e->getMessage());
            $this->responderJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    /**
     * 🔹 Validar datos de la denuncia
     */
    private function validarDatosDenuncia($datos) {
        $errores = [];

        // Validaciones básicas
        if (empty($datos['narracion_hechos'])) {
            $errores[] = 'La narración de los hechos es obligatoria';
        } elseif (strlen($datos['narracion_hechos']) < 50) {
            $errores[] = 'La narración debe tener al menos 50 caracteres';
        }

        if (empty($datos['id_categoria']) || !is_numeric($datos['id_categoria'])) {
            $errores[] = 'Debe seleccionar una categoría válida';
        }

        if (empty($datos['provincia'])) {
            $errores[] = 'La provincia es obligatoria';
        }

        if (empty($datos['canton'])) {
            $errores[] = 'El cantón es obligatorio';
        }

        // Validar datos del denunciante
        if (empty($datos['nombres_denunciante'])) {
            $errores[] = 'Los nombres del denunciante son obligatorios';
        }

        if (empty($datos['apellidos_denunciante'])) {
            $errores[] = 'Los apellidos del denunciante son obligatorios';
        }

        if (empty($datos['cedula_denunciante']) || !is_numeric($datos['cedula_denunciante'])) {
            $errores[] = 'La cédula del denunciante es obligatoria y debe ser numérica';
        }

        if (empty($datos['telefono_denunciante'])) {
            $errores[] = 'El teléfono del denunciante es obligatorio';
        }

        if (empty($datos['correo_denunciante']) || !filter_var($datos['correo_denunciante'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico del denunciante es obligatorio y debe ser válido';
        }

        if (!isset($datos['acepta_politica'])) {
            $errores[] = 'Debe aceptar la política de privacidad';
        }

        return $errores;
    }

    /**
     * 🔹 Procesar usuario denunciante (crear o obtener existente)
     */
    private function procesarUsuarioDenunciante($datos) {
        try {
            $cedula = (int)$datos['cedula_denunciante'];
            
            // Verificar si el usuario ya existe por cédula
            $usuario_existente = $this->usuarioModel->obtenerPorCedula($cedula);
            
            if ($usuario_existente) {
                // Usuario existe, actualizar datos si es necesario
                return $usuario_existente['id_usuario'];
            }

            // Usuario no existe, crear nuevo
            $resultado = $this->usuarioModel->crearUsuario(
                $cedula,
                'denunciante_' . $cedula, // username único
                $datos['nombres_denunciante'],
                $datos['apellidos_denunciante'],
                'O', // sexo no especificado
                'Ecuatoriana', // nacionalidad por defecto
                $datos['telefono_denunciante'],
                $datos['direccion_denunciante'] ?? '',
                $datos['correo_denunciante'],
                $this->generarPasswordTemporal(), // contraseña temporal
                74, // ID del rol "Denunciante"
                null // fecha_verificacion null
            );

            if ($resultado) {
                // Obtener el ID del usuario recién creado
                $nuevo_usuario = $this->usuarioModel->obtenerPorCedula($cedula);
                return $nuevo_usuario['id_usuario'];
            }

            return false;

        } catch (Exception $e) {
            error_log("Error procesando usuario denunciante: " . $e->getMessage());
            return false;
        }
    }

    private function procesarEvidencias($id_denuncia, $archivos, $id_usuario_denunciante) {
    $procesadas = 0;
    $upload_dir = __DIR__ . '/../../uploads/evidencias/';
    
    // Crear directorio si no existe
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // ✅ VERIFICAR que hay archivos válidos
    if (!isset($archivos['name']) || empty($archivos['name'][0])) {
        error_log("📁 No hay evidencias para procesar");
        return 0;
    }

    for ($i = 0; $i < count($archivos['name']); $i++) {
        if ($archivos['error'][$i] === UPLOAD_ERR_OK) {
            $nombre_original = $archivos['name'][$i];
            $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
            $nombre_archivo = 'evidencia_' . $id_denuncia . '_' . time() . '_' . $i . '.' . $extension;
            $ruta_destino = $upload_dir . $nombre_archivo;

            error_log("📁 Procesando archivo: $nombre_original");

            if (move_uploaded_file($archivos['tmp_name'][$i], $ruta_destino)) {
                error_log("✅ Archivo movido correctamente: $nombre_archivo");
                
                try {
                    // Determinar tipo de evidencia
                    $tipo_evidencia = $this->determinarTipoEvidencia($extension);
                    
                    // ✅ CREAR NUEVA INSTANCIA para cada archivo
                    $evidenciaModel = new EvidenciasDenuncia();
                    $evidenciaModel->id_denuncia = $id_denuncia;
                    $evidenciaModel->tipo_evidencia = $tipo_evidencia;
                    $evidenciaModel->nombre_archivo = $nombre_original;
                    $evidenciaModel->ruta_archivo = 'uploads/evidencias/' . $nombre_archivo;
                    $evidenciaModel->tamaño_archivo = $archivos['size'][$i];
                    $evidenciaModel->descripcion = 'Evidencia subida con la denuncia';
                    $evidenciaModel->subido_por = $id_usuario_denunciante; // ✅ USAR EL ID CORRECTO

                    if ($evidenciaModel->crear()) {
                        $procesadas++;
                        error_log("✅ Evidencia guardada en BD: $nombre_original");
                    } else {
                        error_log("❌ Error guardando evidencia en BD: $nombre_original");
                    }
                } catch (Exception $e) {
                    error_log("❌ Error procesando evidencia: " . $e->getMessage());
                }
            } else {
                error_log("❌ Error moviendo archivo: $nombre_original");
            }
        } else {
            error_log("❌ Error en upload: " . $archivos['error'][$i] . " para " . ($archivos['name'][$i] ?? 'archivo desconocido'));
        }
    }

    error_log("📁 Total evidencias procesadas: $procesadas de " . count($archivos['name']));
    return $procesadas;
}

/**
 * 🔹 Procesar evidencia individual
 */
private function procesarEvidenciaSimple($id_denuncia, $archivo) {
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        error_log("❌ Error en upload: " . $archivo['error']);
        return 0;
    }
    
    $upload_dir = __DIR__ . '/../../uploads/evidencias/';
    
    // Crear directorio si no existe
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $nombre_original = $archivo['name'];
    $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
    $nombre_archivo = 'evidencia_' . $id_denuncia . '_' . time() . '.' . $extension;
    $ruta_destino = $upload_dir . $nombre_archivo;
    
    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        try {
            $tipo_evidencia = $this->determinarTipoEvidencia($extension);
            
            $this->evidenciasModel->id_denuncia = $id_denuncia;
            $this->evidenciasModel->tipo_evidencia = $tipo_evidencia;
            $this->evidenciasModel->nombre_archivo = $nombre_original;
            $this->evidenciasModel->ruta_archivo = 'uploads/evidencias/' . $nombre_archivo;
            $this->evidenciasModel->tamaño_archivo = $archivo['size'];
            $this->evidenciasModel->descripcion = 'Evidencia subida con la denuncia';
            $this->evidenciasModel->subido_por = $this->denunciasModel->id_usuario_denunciante;
            
            if ($this->evidenciasModel->crear()) {
                error_log("✅ Evidencia individual guardada: $nombre_original");
                return 1;
            } else {
                error_log("❌ Error guardando evidencia en BD: $nombre_original");
            }
        } catch (Exception $e) {
            error_log("❌ Error procesando evidencia: " . $e->getMessage());
        }
    } else {
        error_log("❌ Error moviendo archivo: $nombre_original");
    }
    
    return 0;
}
    /**
     * 🔹 Determinar tipo de evidencia según extensión
     */
    private function determinarTipoEvidencia($extension) {
        $extension = strtolower($extension);
        
        $tipos = [
            'jpg' => 'FOTO', 'jpeg' => 'FOTO', 'png' => 'FOTO', 'gif' => 'FOTO',
            'mp4' => 'VIDEO', 'avi' => 'VIDEO', 'mov' => 'VIDEO', 'wmv' => 'VIDEO',
            'pdf' => 'DOCUMENTO', 'doc' => 'DOCUMENTO', 'docx' => 'DOCUMENTO', 'txt' => 'DOCUMENTO',
            'mp3' => 'AUDIO', 'wav' => 'AUDIO', 'wma' => 'AUDIO', 'aac' => 'AUDIO'
        ];

        return $tipos[$extension] ?? 'DOCUMENTO';
    }

    /**
     * 🔹 Generar número de denuncia único
     */
    private function generarNumeroDenuncia($id_denuncia) {
        $año = date('Y');
        $mes = date('m');
        $numero = str_pad($id_denuncia, 6, '0', STR_PAD_LEFT);
        return "ECO-{$año}-{$mes}-{$numero}";
    }

    /**
     * 🔹 Actualizar número de denuncia en BD
     */
    private function actualizarNumeroDenuncia($id_denuncia, $numero_denuncia) {
        $query = "UPDATE denuncias SET numero_denuncia = :numero WHERE id_denuncia = :id";
        $stmt = $this->denunciasModel->conn->prepare($query);
        $stmt->execute([
            ':numero' => $numero_denuncia,
            ':id' => $id_denuncia
        ]);
    }
    /**
 * 🔹 Subir evidencia individual (AJAX)
 */
private function subirEvidencia() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->responderJSON([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
        return;
    }

    try {
        if (!isset($_FILES['evidencia']) || $_FILES['evidencia']['error'] !== UPLOAD_ERR_OK) {
            $this->responderJSON([
                'success' => false,
                'message' => 'No se recibió ningún archivo válido'
            ]);
            return;
        }

        $archivo = $_FILES['evidencia'];
        $upload_dir = __DIR__ . '/../../uploads/evidencias/';
        
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Validar tipo de archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $tipos_permitidos = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'pdf', 'doc', 'docx', 'mp3', 'wav'];
        
        if (!in_array(strtolower($extension), $tipos_permitidos)) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Tipo de archivo no permitido'
            ]);
            return;
        }

        // Validar tamaño (máximo 10MB)
        if ($archivo['size'] > 10 * 1024 * 1024) {
            $this->responderJSON([
                'success' => false,
                'message' => 'El archivo es demasiado grande (máximo 10MB)'
            ]);
            return;
        }

        // Generar nombre único
        $nombre_archivo = 'evidencia_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta_destino = $upload_dir . $nombre_archivo;

        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
            $this->responderJSON([
                'success' => true,
                'message' => 'Evidencia subida exitosamente',
                'archivo' => [
                    'nombre_original' => $archivo['name'],
                    'nombre_archivo' => $nombre_archivo,
                    'ruta' => 'uploads/evidencias/' . $nombre_archivo,
                    'tamaño' => $archivo['size'],
                    'tipo' => $this->determinarTipoEvidencia($extension)
                ]
            ]);
        } else {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error al subir el archivo'
            ]);
        }

    } catch (Exception $e) {
        error_log("Error subiendo evidencia: " . $e->getMessage());
        $this->responderJSON([
            'success' => false,
            'message' => 'Error interno del servidor'
        ]);
    }
}

    /**
 * 🔹 Enviar notificación por correo (MÉTODO SIMPLIFICADO)
 */
private function enviarNotificacionDenuncia($usuario, $numero_denuncia, $datos_denuncia = []) {
    try {
        return $this->mailService->enviarConfirmacionDenuncia(
            $usuario['correo'],
            $usuario['nombres'] . ' ' . $usuario['apellidos'],
            $numero_denuncia,
            $datos_denuncia
        );

    } catch (Exception $e) {
        error_log("Error enviando notificación: " . $e->getMessage());
        return false;
    }
}
    /**
     * 🔹 Obtener categorías para AJAX
     */
    private function obtenerCategorias() {
        try {
            $categorias = $this->categoriasModel->obtenerTodas();
            $this->responderJSON([
                'success' => true,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            $this->responderJSON([
                'success' => false,
                'message' => 'Error al obtener categorías'
            ]);
        }
    }

    /**
     * 🔹 Guardar borrador (funcionalidad futura)
     */
    private function guardarBorrador() {
        $this->responderJSON([
            'success' => true,
            'message' => 'Borrador guardado exitosamente'
        ]);
    }

    /**
     * 🔹 Generar contraseña temporal
     */
    private function generarPasswordTemporal() {
        return 'temp_' . bin2hex(random_bytes(6));
    }

    /**
     * 🔹 Responder JSON
     */
    private function responderJSON($data) {
        header('Content-Type: application/json; charset=utf-8');
        if (ob_get_length()) ob_clean();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * 🔹 Mostrar error
     */
    private function mostrarError($titulo, $mensaje) {
        echo "<div class='alert alert-danger'>";
        echo "<h4>{$titulo}</h4>";
        echo "<p>{$mensaje}</p>";
        echo "</div>";
    }
}

// Ejecutar controlador si se accede directamente
if (basename($_SERVER['PHP_SELF']) === 'FormatoDenunciasController.php') {
    try {
        $controller = new FormatoDenunciasController();
        $controller->manejarSolicitud();
    } catch (Throwable $e) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage()
        ]);
    }
}
?>