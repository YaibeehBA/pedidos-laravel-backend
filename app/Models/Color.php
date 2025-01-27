<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    // Especificamos que la tabla se llama 'colores'
    protected $table = 'colores';

    protected $fillable = [
        'nombre',
        'codigo_hex'
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleProducto::class, 'color_id');
    }
}