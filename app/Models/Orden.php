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
        'descuento_total',  
        'fecha_entrega', 
        'estado_pago'
    ];
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // public function detalles()
    // {
    //     return $this->hasMany(DetalleOrden::class);
    // }
    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'orden_id');
    }
    // public function detallesConTallasYColores()
    // {
    //     return $this->hasMany(DetalleOrden::class, 'orden_id')
    //         ->with(['detalleProducto', 'detalleProducto.color', 'detalleProducto.tallas']); // Carga productos con colores y tallas
    // }
    public function detallesConTallasYColores()
{
    return $this->hasMany(DetalleOrden::class, 'orden_id')
        ->with(['detalleProducto.producto:id,nombre', 'detalleProducto.color', 'detalleProducto.tallas']);
}


}
