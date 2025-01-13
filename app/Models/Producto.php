<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre', 
        'descripcion', 
        'categoria_id'
        
    ];
    public function categoria()
    {
        return $this->belongsTo(Categoria::class); 
    }

    public function detalles()
    {
        return $this->hasMany(DetalleProducto::class, 'producto_id');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

}
