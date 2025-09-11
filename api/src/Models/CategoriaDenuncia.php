<?php
// api/src/Models/CategoriaDenuncia.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaDenuncia extends Model
{
    protected $table = 'categorias_denuncia';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre_categoria',
        'descripcion',
        'tipo_principal',
        'icono'
    ];
}

// api/src/Models/Usuario.php
class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null; // No hay campo updated_at
    
    protected $fillable = [
        'cedula',
        'username', 
        'nombres',
        'apellidos',
        'sexo',
        'nacionalidad',
        'telefono_contacto',
        'direccion_domicilio',
        'correo',
        'password',
        'id_rol',
        'id_estado'
    ];
    
    protected $hidden = ['password'];
}

// api/src/Models/EstadoDenuncia.php
class EstadoDenuncia extends Model
{
    protected $table = 'estados_denuncia';
    protected $primaryKey = 'id_estado_denuncia';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre_estado',
        'descripcion',
        'color'
    ];
}

// api/src/Models/InstitucionResponsable.php
class InstitucionResponsable extends Model
{
    protected $table = 'instituciones_responsables';
    protected $primaryKey = 'id_institucion';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre_institucion',
        'siglas',
        'tipo_institucion',
        'contacto_email',
        'contacto_telefono',
        'responsable_nombre',
        'responsable_cargo',
        'activo'
    ];
    
    protected $casts = [
        'activo' => 'boolean'
    ];
}

// api/src/Models/EvidenciaDenuncia.php
class EvidenciaDenuncia extends Model
{
    protected $table = 'evidencias_denuncia';
    protected $primaryKey = 'id_evidencia';
    
    const CREATED_AT = 'fecha_subida';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'id_denuncia',
        'tipo_evidencia',
        'nombre_archivo',
        'ruta_archivo',
        'tamaño_archivo',
        'descripcion',
        'subido_por'
    ];
}

// api/src/Models/SeguimientoDenuncia.php
class SeguimientoDenuncia extends Model
{
    protected $table = 'seguimiento_denuncias';
    protected $primaryKey = 'id_seguimiento';
    
    const CREATED_AT = 'fecha_actualizacion';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'id_denuncia',
        'id_estado_anterior',
        'id_estado_nuevo',
        'id_usuario_responsable',
        'comentario',
        'es_visible_denunciante'
    ];
    
    protected $casts = [
        'es_visible_denunciante' => 'boolean',
        'fecha_actualizacion' => 'datetime'
    ];
    
    public function estadoAnterior()
    {
        return $this->belongsTo(EstadoDenuncia::class, 'id_estado_anterior');
    }
    
    public function estadoNuevo()
    {
        return $this->belongsTo(EstadoDenuncia::class, 'id_estado_nuevo');
    }
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_responsable');
    }
}

// api/src/Models/AsignacionDenuncia.php
class AsignacionDenuncia extends Model
{
    protected $table = 'asignaciones_denuncia';
    protected $primaryKey = 'id_asignacion';
    
    const CREATED_AT = 'fecha_asignacion';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'id_denuncia',
        'id_usuario_supervisor',
        'id_institucion_asignada',
        'comentario_asignacion',
        'prioridad',
        'estado_asignacion'
    ];
    
    protected $casts = [
        'fecha_asignacion' => 'datetime'
    ];
    
    // ✅ AGREGAR ESTA RELACIÓN QUE FALTABA
    public function institucion()
    {
        return $this->belongsTo(InstitucionResponsable::class, 'id_institucion_asignada');
    }
    
    public function denuncia()
    {
        return $this->belongsTo(Denuncia::class, 'id_denuncia');
    }
    
    public function supervisor()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_supervisor');
    }
}

// api/src/Models/Notificacion.php
class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';
    
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'id_usuario_destino',
        'id_denuncia',
        'tipo_notificacion',
        'titulo',
        'mensaje',
        'leida'
    ];
    
    protected $casts = [
        'leida' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_lectura' => 'datetime'
    ];
}
?>