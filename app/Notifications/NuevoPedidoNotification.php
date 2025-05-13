<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoPedidoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $orden;
    public $tries = 3;
    public $timeout = 30;

    public function __construct($orden)
    {
        $this->orden = $orden;
    }

    public function via($notifiable)
    {
        return ['database']; // Solo notificación en la base de datos
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'admin', // Tipo de notificación
            'orden_id' => $this->orden->id,
            'mensaje' => "Se ha recibido un nuevo pedido #{$this->orden->id}. Revisa los detalles y comienza el proceso de producción."

        ];
    }
}