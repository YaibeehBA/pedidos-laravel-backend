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
        'talla_id',
        'cantidad', 
        'precio_base', 
        'precio_unitario', 
        'descuento_unitario',
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
    public function obtenerNombreProducto()
    {
        return $this->detalleProducto->nombre; // Nombre del producto asociado al detalle
    }
    public function talla()
    {
        return $this->belongsTo(Talla::class, 'talla_id'); // Relación con la talla
    }
}
