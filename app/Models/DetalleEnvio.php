<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleEnvio extends Model
{
    use HasFactory;
      protected $fillable = [
        'orden_id',
        'tipo_envio',
        'ciudad_envio_id',
        'direccion',
        'referencia',
        'peso_total',
        'costo_envio',
        'estado_envio'
    ];
     public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function ciudad()
    {
        return $this->belongsTo(CiudadEnvio::class, 'ciudad_envio_id');
    }
}
