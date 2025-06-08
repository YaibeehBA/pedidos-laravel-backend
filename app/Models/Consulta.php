<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;
     protected $fillable = [
        'nombre_completo',
        'correo_electronico',
        'telefono',
        'mensaje',
        'leido',
    ];

}
