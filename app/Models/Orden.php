<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;
    protected $table = 'ordenes';

    protected $fillable = [
        'usuario_id', 
        'estado', 
        'monto_total', 
        'fecha_entrega', 
        'estado_pago'
    ];
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class);
    }
}
