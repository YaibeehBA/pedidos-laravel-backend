<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    use HasFactory;
    protected $table = 'detalles_orden';

    protected $fillable = [
        'orden_id', 
        'detalles_productos_id', 
        'cantidad', 
        'precio_unitario', 
        'subtotal'
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    // Relación con DetalleProducto
    public function detalleProducto()
    {
        return $this->belongsTo(DetalleProducto::class, 'detalles_productos_id');
    }
}
