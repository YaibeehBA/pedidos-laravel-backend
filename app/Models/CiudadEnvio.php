<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CiudadEnvio extends Model
{
    use HasFactory;
     protected $fillable = [
        'nombre', 
        'precio_envio'
    ];
    
   public static function getCiudadOrigen()
{
    return self::firstOrCreate(
        ['nombre' => 'Riobamba'],
        ['precio_envio' => 0.00]
    )->id; 
}
}
