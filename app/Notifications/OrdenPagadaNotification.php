<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrdenPagadaNotification extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pago Confirmado - Orden #' . $this->orden->id)
            ->greeting('Hola ' . $notifiable->name)
            ->greeting('Hola ' . $this->orden->usuario->nombre . ' ' . $this->orden->usuario->apellido)
            ->line("El pago de tu orden #{$this->orden->id} ha sido confirmado.")
            ->line("Estamos preparando tu pedido.")
            ->action('Ver mi pedido', url(config('app.frontend_url') . '/Pedidos'))
            ->line('Gracias por tu preferencia.')
            ->salutation('Saludos, ' . config('app.name'));
    }

    public function toArray($notifiable)
    {
        
        return [
            'orden_id' => $this->orden->id,
            'estado' => 'Entregando',
            'mensaje' => "Tu orden #{$this->orden->id} está en proceso de entrega."
        ];
    }
}
