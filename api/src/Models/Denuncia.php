<?php
// api/src/Models/Denuncia.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denuncia extends Model
{
    protected $table = 'denuncias';
    protected $primaryKey = 'id_denuncia';
    
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    
    protected $fillable = [
        'titulo',
        'descripcion',
        'id_categoria',
        'id_usuario_denunciante',
        'provincia',
        'canton',
        'parroquia',
        'direccion_especifica',
        'fecha_ocurrencia',
        'gravedad',
        'servidor_municipal',
        'entidad_municipal',
        'informacion_adicional_denunciado',
        'requiere_atencion_prioritaria',
        'acepta_politica_privacidad'
    ];
    
    protected $casts = [
        'fecha_ocurrencia' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'fecha_resolucion' => 'datetime',
        'requiere_atencion_prioritaria' => 'boolean',
        'acepta_politica_privacidad' => 'boolean'
    ];
    
    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaDenuncia::class, 'id_categoria');
    }
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_denunciante');
    }
    
    public function estado()
    {
        return $this->belongsTo(EstadoDenuncia::class, 'id_estado_denuncia');
    }
    
    public function institucion()
    {
        return $this->belongsTo(InstitucionResponsable::class, 'id_institucion_asignada');
    }
    
    public function evidencias()
    {
        return $this->hasMany(EvidenciaDenuncia::class, 'id_denuncia');
    }
    
    public function seguimientos()
    {
        return $this->hasMany(SeguimientoDenuncia::class, 'id_denuncia')
                   ->orderBy('fecha_actualizacion', 'desc');
    }
    
    public function asignaciones()
    {
        return $this->hasMany(AsignacionDenuncia::class, 'id_denuncia');
    }
    
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_denuncia');
    }
}
?>