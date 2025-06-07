<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionEnvio extends Model
{
    use HasFactory;
     protected $fillable = [
        'precio_por_kg'
    ];
}
