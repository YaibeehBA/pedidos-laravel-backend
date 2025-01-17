<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Talla extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre'
    ];

    // public function detalles()
    // {
    //     return $this->hasMany(DetalleProducto::class, 'talla_id');
    // }

    public function detallesProductos()
    {
        return $this->belongsToMany(DetalleProducto::class, 'detalle_producto_talla')
                    ->withTimestamps();
    }

}
