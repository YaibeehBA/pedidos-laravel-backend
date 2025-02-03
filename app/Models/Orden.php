<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\OrdenPagadaNotification;
use App\Notifications\OrdenAtrasadaNotification;
use App\Notifications\OrdenEntregadaNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    protected $with = ['usuario'];

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

// public function notifyStatusUpdate()
// {
//     // Registrar el objeto usuario en el log
//     Log::debug('Usuario asociado a la orden:', [$this->usuario]);

//     switch($this->estado) {
//         case 'Atrasado':
//             $this->usuario->notify(new OrdenAtrasadaNotification($this));
//             $this->fecha_entrega = Carbon::parse($this->fecha_entrega)->addDay();
//             $this->save();
//             break;
//         case 'Entregando':
//             $this->usuario->notify(new OrdenEntregadaNotification($this));
//             break;
//         case 'Pagado':
//             $this->usuario->notify(new OrdenPagadaNotification($this));
//             break;
//     }
// }
public function notifyStatusUpdate()
{
    // Ensure usuario is loaded
    if (!$this->relationLoaded('usuario')) {
        $this->load('usuario');
    }

    // Log detailed information
    Log::debug('Notificación de actualización de estado:', [
        'orden_id' => $this->id,
        'estado' => $this->estado,
        'usuario_loaded' => $this->relationLoaded('usuario'),
        'usuario' => $this->usuario
    ]);

    switch($this->estado) {
        case 'Atrasado':
            $this->usuario->notify(new OrdenAtrasadaNotification($this));
            // $this->usuario->notify((new OrdenAtrasadaNotification($this))->delay(now()->addHour()));

            $this->fecha_entrega = Carbon::parse($this->fecha_entrega)->addDay();
            $this->save();
            break;
        case 'Entregando':
            $this->usuario->notify(new OrdenEntregadaNotification($this));
            break;
        case 'Pagado':
            $this->usuario->notify(new OrdenPagadaNotification($this));
            break;
    }
}
}