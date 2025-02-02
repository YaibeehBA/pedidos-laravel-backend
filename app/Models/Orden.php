<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\OrdenPagadaNotification;
use App\Notifications\OrdenAtrasadaNotification;
use App\Notifications\OrdenEntregadaNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

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

public function notifyStatusUpdate()
    {
        if ($this->estado === 'Atrasado') {
            $this->usuario->notify(new OrdenAtrasadaNotification($this));
            $this->fecha_entrega = Carbon::parse($this->fecha_entrega)->addDay();
            $this->save();
        } elseif ($this->estado === 'Entregando') {
            $this->usuario->notify(new OrdenEntregadaNotification($this));
        } elseif ($this->estado === 'Pagado') {
            $this->usuario->notify(new OrdenPagadaNotification($this));
        }
    }

}
