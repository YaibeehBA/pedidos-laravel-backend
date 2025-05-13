<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    use HasFactory;
    protected $table = 'descuentos';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'valor',
        'activo',
        'cantidad_minima',
        'aplica_todos_productos',
        'fecha_inicio',
        'fecha_fin'
    ];
    
    protected $casts = [
        'activo' => 'boolean',
        'aplica_todos_productos' => 'boolean',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime'
    ];

    public function detallesProductos()
    {
        return $this->belongsToMany(DetalleProducto::class, 'descuento_producto', 'descuento_id', 'detalle_producto_id')
                    ->withTimestamps();
    }

    public function esValido()
    {
        if (!$this->activo) {
            return false;
        }

        $ahora = now();
        
        // Si no hay fechas definidas, el descuento es válido
        if ($this->fecha_inicio === null && $this->fecha_fin === null) {
            return true;
        }

        // Si solo hay fecha de inicio, verificar que ya haya iniciado
        if ($this->fecha_inicio !== null && $this->fecha_fin === null) {
            return $ahora >= $this->fecha_inicio;
        }

        // Si solo hay fecha de fin, verificar que no haya terminado
        if ($this->fecha_inicio === null && $this->fecha_fin !== null) {
            return $ahora <= $this->fecha_fin;
        }

        // Si hay ambas fechas, verificar que esté dentro del rango
        return $ahora >= $this->fecha_inicio && $ahora <= $this->fecha_fin;
    }

    public function calcularDescuento($subtotal, $cantidadProductos)
    {
        if (!$this->esValido()) {
            return 0;
        }

        if ($cantidadProductos < $this->cantidad_minima) {
            return 0;
        }

        switch ($this->tipo) {
            case 'porcentaje':
                return ($subtotal * $this->valor) / 100;
            case 'monto_fijo':
                return $this->valor;
            default:
                return 0;
        }
    }
}
