<?php
// api/src/Controllers/DenunciaController.php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Denuncia;
use App\Models\CategoriaDenuncia;
use App\Models\Usuario;
use App\Models\EvidenciaDenuncia;
use App\Utils\ResponseUtil;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class DenunciaController
{
    /**
     * API 1: CREAR NUEVA DENUNCIA
     * POST /denuncias
     */
    public function crear(Request $request, Response $response): Response
{
    try {
        $data = $request->getParsedBody();
        
        // Validación básica de datos requeridos
        $camposRequeridos = [
            'titulo', 
            'descripcion', 
            'id_categoria', 
            'cedula_denunciante',
            'provincia', 
            'canton', 
            'parroquia',
            'gravedad',
            'acepta_politica_privacidad'
        ];
        
        foreach ($camposRequeridos as $campo) {
            if (empty($data[$campo])) {
                return ResponseUtil::badRequest("El campo '$campo' es requerido");
            }
        }
        
        // Validar que la categoría exista
        $categoria = CategoriaDenuncia::find($data['id_categoria']);
        if (!$categoria) {
            return ResponseUtil::badRequest('La categoría seleccionada no existe');
        }
        
        // Buscar o crear usuario por cédula
        $usuario = Usuario::where('cedula', $data['cedula_denunciante'])->first();
        if (!$usuario) {
            return ResponseUtil::badRequest('No se encontró un usuario con la cédula proporcionada');
        }
        
        // Validar política de privacidad
        if (!filter_var($data['acepta_politica_privacidad'], FILTER_VALIDATE_BOOLEAN)) {
            return ResponseUtil::badRequest('Debe aceptar la política de privacidad');
        }
        
        // Validar gravedad
        $gravedadValida = ['BAJA', 'MEDIA', 'ALTA', 'CRITICA'];
        if (!in_array($data['gravedad'], $gravedadValida)) {
            return ResponseUtil::badRequest('Gravedad inválida. Valores permitidos: ' . implode(', ', $gravedadValida));
        }
        
        // Generar número de denuncia con formato ECO-YYYY-MM-NNNNNN ANTES de crear
        $anio = date('Y');
        $mes = date('m');
        
        // Buscar el último número del mes actual
        $ultimaDenuncia = Denuncia::where('numero_denuncia', 'LIKE', "ECO-{$anio}-{$mes}-%")
                                  ->orderBy('numero_denuncia', 'desc')
                                  ->first();
        
        if ($ultimaDenuncia) {
            // Extraer el número secuencial y sumarle 1
            $ultimoNumero = intval(substr($ultimaDenuncia->numero_denuncia, -6));
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            // Primer número del mes
            $nuevoNumero = 1;
        }
        
        $numeroGenerado = sprintf('ECO-%s-%s-%06d', $anio, $mes, $nuevoNumero);
        
        // Crear la denuncia
        $denuncia = new Denuncia();
        
        // Asignar el número generado manualmente
        $denuncia->numero_denuncia = $numeroGenerado;
        $denuncia->titulo = $data['titulo'];
        $denuncia->descripcion = $data['descripcion'];
        $denuncia->id_categoria = $data['id_categoria'];
        $denuncia->id_usuario_denunciante = $usuario->id_usuario;
        $denuncia->id_estado_denuncia = 1; // Estado inicial: Pendiente
        $denuncia->provincia = $data['provincia'];
        $denuncia->canton = $data['canton'];
        $denuncia->parroquia = $data['parroquia'];
        $denuncia->direccion_especifica = $data['direccion_especifica'] ?? null;
        $denuncia->gravedad = $data['gravedad'];
        $denuncia->servidor_municipal = $data['servidor_municipal'] ?? '';
        $denuncia->entidad_municipal = $data['entidad_municipal'] ?? '';
        $denuncia->informacion_adicional_denunciado = $data['informacion_adicional_denunciado'] ?? '';
        $denuncia->requiere_atencion_prioritaria = filter_var($data['requiere_atencion_prioritaria'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $denuncia->acepta_politica_privacidad = true;
        
        // Fecha de ocurrencia
        if (!empty($data['fecha_ocurrencia'])) {
            $denuncia->fecha_ocurrencia = date('Y-m-d H:i:s', strtotime($data['fecha_ocurrencia']));
        } else {
            $denuncia->fecha_ocurrencia = date('Y-m-d H:i:s');
        }
        
        $denuncia->save();
        
        // Procesar evidencias si existen
        $evidenciasCreadas = [];
        if (!empty($data['evidencias'])) {
            foreach ($data['evidencias'] as $index => $evidencia) {
                if (!empty($evidencia['nombre_archivo']) && !empty($evidencia['tipo_evidencia'])) {
                    $nuevaEvidencia = new EvidenciaDenuncia();
                    $nuevaEvidencia->id_denuncia = $denuncia->id_denuncia;
                    $nuevaEvidencia->tipo_evidencia = strtoupper($evidencia['tipo_evidencia']);
                    $nuevaEvidencia->nombre_archivo = $evidencia['nombre_archivo'];
                    $nuevaEvidencia->ruta_archivo = $evidencia['ruta_archivo'] ?? 'uploads/evidencias/mobile_' . $denuncia->id_denuncia . '_' . time() . '_' . $index . '.jpg';
                    $nuevaEvidencia->tamaño_archivo = $evidencia['tamaño_archivo'] ?? 0;
                    $nuevaEvidencia->descripcion = $evidencia['descripcion'] ?? 'Evidencia subida desde móvil';
                    $nuevaEvidencia->subido_por = $usuario->id_usuario;
                    $nuevaEvidencia->save();
                    
                    $evidenciasCreadas[] = [
                        'id' => $nuevaEvidencia->id_evidencia,
                        'tipo' => $nuevaEvidencia->tipo_evidencia,
                        'archivo' => $nuevaEvidencia->nombre_archivo
                    ];
                }
            }
        }
        
        // Cargar datos completos para la respuesta
        $denunciaCompleta = Denuncia::with([
            'categoria',
            'usuario:id_usuario,nombres,apellidos,correo',
            'estado',
            'evidencias'
        ])->find($denuncia->id_denuncia);
        
        $respuesta = [
            'denuncia' => [
                'id' => $denunciaCompleta->id_denuncia,
                'numero_denuncia' => $denunciaCompleta->numero_denuncia,
                'titulo' => $denunciaCompleta->titulo,
                'descripcion' => $denunciaCompleta->descripcion,
                'gravedad' => $denunciaCompleta->gravedad,
                'estado' => [
                    'id' => $denunciaCompleta->estado->id_estado_denuncia,
                    'nombre' => $denunciaCompleta->estado->nombre_estado,
                    'color' => $denunciaCompleta->estado->color
                ],
                'categoria' => [
                    'id' => $denunciaCompleta->categoria->id_categoria,
                    'nombre' => $denunciaCompleta->categoria->nombre_categoria,
                    'tipo' => $denunciaCompleta->categoria->tipo_principal,
                    'icono' => $denunciaCompleta->categoria->icono
                ],
                'ubicacion' => [
                    'provincia' => $denunciaCompleta->provincia,
                    'canton' => $denunciaCompleta->canton,
                    'parroquia' => $denunciaCompleta->parroquia,
                    'direccion_especifica' => $denunciaCompleta->direccion_especifica
                ],
                'fecha_creacion' => $denunciaCompleta->fecha_creacion->format('Y-m-d H:i:s'),
                'fecha_ocurrencia' => $denunciaCompleta->fecha_ocurrencia->format('Y-m-d H:i:s'),
                'requiere_atencion_prioritaria' => $denunciaCompleta->requiere_atencion_prioritaria,
                'evidencias_subidas' => count($evidenciasCreadas),
                'evidencias' => $evidenciasCreadas
            ],
            'mensaje_usuario' => '¡Denuncia creada exitosamente! Se le ha asignado el número: ' . $denunciaCompleta->numero_denuncia . '. Guárdelo para consultar el estado de su denuncia.',
            'instrucciones' => [
                'numero_seguimiento' => $denunciaCompleta->numero_denuncia,
                'como_consultar' => 'Use el número de denuncia para consultar el estado en cualquier momento',
                'tiempo_estimado' => 'Recibirá una respuesta en un plazo máximo de 15 días hábiles'
            ]
        ];
        
        return ResponseUtil::created($respuesta, 'Denuncia creada exitosamente');
        
    } catch (Exception $e) {
        error_log("Error creando denuncia: " . $e->getMessage());
        return ResponseUtil::error('Error interno del servidor al crear la denuncia', 500, [
            'error_details' => $e->getMessage()
        ]);
    }
}
    
    /**
     * API 2: CONSULTAR ESTADO DE DENUNCIA POR CÓDIGO
     * GET /denuncias/consultar/{numero_denuncia}
     */
    public function consultarEstado(Request $request, Response $response, array $args): Response
    {
        try {
            $numeroDenuncia = $args['numero_denuncia'] ?? '';
            
            if (empty($numeroDenuncia)) {
                return ResponseUtil::badRequest('Debe proporcionar el número de denuncia');
            }
            
            // Buscar la denuncia por número
            $denuncia = Denuncia::with([
                'categoria',
                'usuario:id_usuario,nombres,apellidos,correo,telefono_contacto',
                'estado',
                'institucion',
                'evidencias',
                'seguimientos' => function($query) {
                    $query->with(['estadoAnterior', 'estadoNuevo', 'usuario:id_usuario,nombres,apellidos'])
                          ->where('es_visible_denunciante', 1)
                          ->orderBy('fecha_actualizacion', 'desc');
                },
                'asignaciones' => function($query) {
                    $query->where('estado_asignacion', 'ACTIVO')
                          ->with(['institucion' => function($q) {
                              $q->select('id_institucion', 'nombre_institucion', 'siglas', 'contacto_email', 'contacto_telefono');
                          }]);
                }
            ])->where('numero_denuncia', $numeroDenuncia)->first();
            
            if (!$denuncia) {
                return ResponseUtil::notFound('No se encontró ninguna denuncia con el número proporcionado');
            }
            
            // Calcular días transcurridos
            $diasTranscurridos = $denuncia->fecha_creacion->diffInDays(date('Y-m-d'));
            $diasHabiles = $this->calcularDiasHabiles($denuncia->fecha_creacion, date('Y-m-d'));
            
            // Determinar estado del progreso
            $progreso = $this->calcularProgreso($denuncia->estado->id_estado_denuncia);
            
            // Construir historial de seguimiento
            $historial = [];
            foreach ($denuncia->seguimientos as $seguimiento) {
                $historial[] = [
                    'fecha' => $seguimiento->fecha_actualizacion->format('Y-m-d H:i:s'),
                    'fecha_legible' => $seguimiento->fecha_actualizacion->format('d/m/Y H:i'),
                    'estado_anterior' => $seguimiento->estadoAnterior ? [
                        'nombre' => $seguimiento->estadoAnterior->nombre_estado,
                        'color' => $seguimiento->estadoAnterior->color
                    ] : null,
                    'estado_nuevo' => [
                        'nombre' => $seguimiento->estadoNuevo->nombre_estado,
                        'color' => $seguimiento->estadoNuevo->color
                    ],
                    'comentario' => $seguimiento->comentario,
                    'responsable' => $seguimiento->usuario ? 
                        $seguimiento->usuario->nombres . ' ' . $seguimiento->usuario->apellidos : 
                        'Sistema',
                    'dias_transcurridos' => $seguimiento->fecha_actualizacion->diffInDays($denuncia->fecha_creacion)
                ];
            }
            
            // Información de contacto de la institución responsable
            $institucionInfo = null;
            if ($denuncia->asignaciones->count() > 0) {
                $asignacion = $denuncia->asignaciones->first();
                if ($asignacion->institucion) {
                    $institucionInfo = [
                        'nombre' => $asignacion->institucion->nombre_institucion,
                        'siglas' => $asignacion->institucion->siglas,
                        'email' => $asignacion->institucion->contacto_email,
                        'telefono' => $asignacion->institucion->contacto_telefono,
                        'prioridad' => $asignacion->prioridad,
                        'fecha_asignacion' => $asignacion->fecha_asignacion
                    ];
                }
            }
            
            $respuesta = [
                'denuncia' => [
                    'numero_denuncia' => $denuncia->numero_denuncia,
                    'titulo' => $denuncia->titulo,
                    'descripcion' => $denuncia->descripcion,
                    'gravedad' => $denuncia->gravedad,
                    'estado_actual' => [
                        'id' => $denuncia->estado->id_estado_denuncia,
                        'nombre' => $denuncia->estado->nombre_estado,
                        'descripcion' => $denuncia->estado->descripcion,
                        'color' => $denuncia->estado->color,
                        'progreso_porcentaje' => $progreso['porcentaje'],
                        'fase' => $progreso['fase']
                    ],
                    'categoria' => [
                        'nombre' => $denuncia->categoria->nombre_categoria,
                        'tipo' => $denuncia->categoria->tipo_principal,
                        'icono' => $denuncia->categoria->icono
                    ],
                    'ubicacion' => [
                        'provincia' => $denuncia->provincia,
                        'canton' => $denuncia->canton,
                        'parroquia' => $denuncia->parroquia,
                        'direccion_especifica' => $denuncia->direccion_especifica
                    ],
                    'fechas' => [
                        'creacion' => $denuncia->fecha_creacion->format('Y-m-d H:i:s'),
                        'creacion_legible' => $denuncia->fecha_creacion->format('d/m/Y H:i'),
                        'ocurrencia' => $denuncia->fecha_ocurrencia->format('Y-m-d H:i:s'),
                        'resolucion' => $denuncia->fecha_resolucion ? 
                            $denuncia->fecha_resolucion->format('Y-m-d H:i:s') : null,
                        'dias_transcurridos' => $diasTranscurridos,
                        'dias_habiles' => $diasHabiles
                    ],
                    'denunciante' => [
                        'nombre_completo' => $denuncia->usuario->nombres . ' ' . $denuncia->usuario->apellidos,
                        'email' => $denuncia->usuario->correo,
                        'telefono' => $denuncia->usuario->telefono_contacto
                    ],
                    'prioridad_atencion' => $denuncia->requiere_atencion_prioritaria,
                    'evidencias_count' => $denuncia->evidencias->count(),
                    'institucion_responsable' => $institucionInfo
                ],
                'historial_seguimiento' => $historial,
                'estadisticas' => [
                    'total_seguimientos' => count($historial),
                    'tiempo_promedio_respuesta' => $this->calcularTiempoPromedioRespuesta($denuncia),
                    'esta_en_plazo' => $diasHabiles <= 15,
                    'dias_restantes_plazo' => max(0, 15 - $diasHabiles)
                ],
                'acciones_disponibles' => $this->obtenerAccionesDisponibles($denuncia->estado->id_estado_denuncia),
                'informacion_adicional' => [
                    'puede_aportar_evidencias' => in_array($denuncia->estado->id_estado_denuncia, [1, 2, 3]),
                    'permite_comentarios' => in_array($denuncia->estado->id_estado_denuncia, [1, 2, 3]),
                    'requiere_seguimiento' => !in_array($denuncia->estado->id_estado_denuncia, [4, 5, 6])
                ]
            ];
            
            return ResponseUtil::success($respuesta, 'Consulta de denuncia exitosa');
            
        } catch (Exception $e) {
            error_log("Error consultando denuncia: " . $e->getMessage());
            return ResponseUtil::error('Error interno del servidor al consultar la denuncia', 500, [
                'error_details' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtener todas las categorías disponibles
     * GET /denuncias/categorias
     */
    public function obtenerCategorias(Request $request, Response $response): Response
    {
        try {
            $categorias = CategoriaDenuncia::orderBy('tipo_principal')
                                          ->orderBy('nombre_categoria')
                                          ->get();
            
            $categoriasAgrupadas = [
                'AMBIENTAL' => [],
                'OBRAS_PUBLICAS' => []
            ];
            
            foreach ($categorias as $categoria) {
                $categoriasAgrupadas[$categoria->tipo_principal][] = [
                    'id' => $categoria->id_categoria,
                    'nombre' => $categoria->nombre_categoria,
                    'descripcion' => $categoria->descripcion,
                    'icono' => $categoria->icono
                ];
            }
            
            return ResponseUtil::success([
                'categorias' => $categoriasAgrupadas,
                'total' => $categorias->count()
            ], 'Categorías obtenidas exitosamente');
            
        } catch (Exception $e) {
            return ResponseUtil::error('Error al obtener categorías', 500);
        }
    }
    
    // ===== MÉTODOS AUXILIARES =====
    
    private function calcularDiasHabiles($fechaInicio, $fechaFin)
    {
        $dias = 0;
        $fecha = $fechaInicio->copy();
        
        while ($fecha->lte($fechaFin)) {
            if ($fecha->isWeekday()) {
                $dias++;
            }
            $fecha->addDay();
        }
        
        return $dias;
    }
    
    private function calcularProgreso($estadoId)
    {
        $estados = [
            1 => ['porcentaje' => 10, 'fase' => 'Recibida'],      // Pendiente
            2 => ['porcentaje' => 30, 'fase' => 'En evaluación'], // En Revisión  
            3 => ['porcentaje' => 60, 'fase' => 'En proceso'],    // En Proceso
            4 => ['porcentaje' => 100, 'fase' => 'Completada'],   // Resuelto
            5 => ['porcentaje' => 100, 'fase' => 'Finalizada'],   // Cerrado
            6 => ['porcentaje' => 0, 'fase' => 'Rechazada']       // Rechazado
        ];
        
        return $estados[$estadoId] ?? ['porcentaje' => 0, 'fase' => 'Desconocida'];
    }
    
    private function calcularTiempoPromedioRespuesta($denuncia)
    {
        if ($denuncia->seguimientos->count() < 2) {
            return 'Sin datos suficientes';
        }
        
        $tiempos = [];
        $seguimientos = $denuncia->seguimientos->sortBy('fecha_actualizacion');
        
        for ($i = 1; $i < $seguimientos->count(); $i++) {
            $anterior = $seguimientos->values()[$i-1];
            $actual = $seguimientos->values()[$i];
            $tiempos[] = $anterior->fecha_actualizacion->diffInHours($actual->fecha_actualizacion);
        }
        
        $promedio = array_sum($tiempos) / count($tiempos);
        
        if ($promedio < 24) {
            return round($promedio, 1) . ' horas';
        } else {
            return round($promedio / 24, 1) . ' días';
        }
    }
    
    private function obtenerAccionesDisponibles($estadoId)
    {$acciones = [
            1 => ['consultar', 'aportar_evidencias', 'contactar_soporte'],          // Pendiente
            2 => ['consultar', 'aportar_evidencias', 'contactar_institucion'],     // En Revisión
            3 => ['consultar', 'seguir_progreso', 'contactar_institucion'],        // En Proceso
            4 => ['consultar', 'descargar_resolucion', 'calificar_servicio'],      // Resuelto
            5 => ['consultar', 'historico_completo'],                              // Cerrado
            6 => ['consultar', 'apelar_decision', 'contactar_soporte']             // Rechazado
        ];
        
        return $acciones[$estadoId] ?? ['consultar'];
    }
}
?>