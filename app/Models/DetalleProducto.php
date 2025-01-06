<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleProducto extends Model
{
    use HasFactory;
    protected $table = 'detalles_productos';
    protected $fillable = [
        'producto_id',
        'color_id',
        'talla_id',
        'precio_base',
        
        'imagen_url'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function talla()
    {
        return $this->belongsTo(Talla::class, 'talla_id');
    }
}
