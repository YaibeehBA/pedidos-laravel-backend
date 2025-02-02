<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrdenEntregadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $orden;

    public function __construct($orden)
    {
        $this->orden = $orden;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Orden en Entrega')
            ->line("Tu orden #{$this->orden->id} está en proceso de entrega.")
            ->action('Ver mis pedidos', url(env('FRONTEND_URL') . '/Pedidos'))
            ->line('Gracias por comprar con nosotros.');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->orden->id,
            'estado' => 'Entregando',
            'mensaje' => "Tu orden #{$this->orden->id} está en entrega.",
        ];
    }
}
