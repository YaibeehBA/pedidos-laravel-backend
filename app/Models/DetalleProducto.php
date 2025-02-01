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
        // 'talla_id',
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

    public function tallas()
    {
        return $this->belongsToMany(Talla::class, 'detalle_producto_talla')
                    ->withTimestamps(); // Para agregar timestamps a la tabla intermedia
    }
    
    public function descuentos()
    {
        return $this->belongsToMany(Descuento::class, 'descuento_producto', 'detalle_producto_id', 'descuento_id')
                    ->withTimestamps();
    }

    public function obtenerDescuentosActivos()
    {
        // Obtener descuentos específicos del producto
        $descuentosEspecificos = $this->descuentos()
            ->where('activo', true)
            ->get();

        // Obtener descuentos generales (que aplican a todos los productos)
        $descuentosGenerales = Descuento::where('aplica_todos_productos', true)
            ->where('activo', true)
            ->get();

        // Combinar ambos conjuntos de descuentos
        return $descuentosEspecificos->concat($descuentosGenerales)
            ->filter(function ($descuento) {
                return $descuento->esValido();
            });
    }

    public function obtenerMejorDescuento($cantidad = 1)
    {
        $subtotal = $this->precio_base * $cantidad;
        $descuentos = $this->obtenerDescuentosActivos();
        
        return $descuentos->max(function ($descuento) use ($subtotal, $cantidad) {
            return $descuento->calcularDescuento($subtotal, $cantidad);
        }) ?? 0;
    }
}
