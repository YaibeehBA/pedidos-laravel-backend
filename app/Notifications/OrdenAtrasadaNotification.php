<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class OrdenAtrasadaNotification extends Notification implements ShouldQueue
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

    // public function toMail($notifiable)
    // {
    //     $fecha_entrega = Carbon::parse($this->orden->fecha_entrega)
    //         ->timezone(config('app.timezone'))
    //         ->format('j/n/Y');

    //     return (new MailMessage)
    //         ->subject('⚠️ Tu orden #' . $this->orden->id . ' está atrasada')
    //         ->greeting('Hola ' . $this->orden->usuario->nombre . ' ' . $this->orden->usuario->apellido)
    //         ->line("Lamentamos informarte que tu orden #{$this->orden->id} ha sufrido un retraso en la entrega.")
    //         ->line("Fecha estimada de entrega: {$fecha_entrega}")
    //         ->line("Estamos trabajando para que tu pedido llegue lo antes posible.")
    //         // ->action('Ver mi pedido', url(config('FRONTEND_URL') . '/Pedidos'))
    //         ->action('Ver mis pedidos 2', url(env('FRONTEND_URL') . '/Pedidos'))
    //         ->line('Gracias por tu paciencia y comprensión.')
    //         ->salutation('Saludos, ' . config('app.name'));
    // }

    public function toMail($notifiable)
    {
        $fecha_entrega = Carbon::parse($this->orden->fecha_entrega)
            ->timezone(config('app.timezone'))
            ->format('j/n/Y');

        return (new MailMessage)
            ->subject('⚠️ Tu orden #' . $this->orden->id . ' está atrasada')
            ->view('emails.orden_atrasada', [
                'orden' => $this->orden,
                'fecha_entrega' => $fecha_entrega,
            ]);
    }

    public function toArray($notifiable)
    {
        $fecha_entrega = Carbon::parse($this->orden->fecha_entrega)
            ->timezone(config('app.timezone'))
            ->format('j/n/Y');

        return [
            'orden_id' => $this->orden->id,
            'estado' => 'Atrasado',
            'fecha' => $fecha_entrega,
            'mensaje' => "Tu orden #{$this->orden->id} está retrasada. Estamos trabajando en la entrega."
        ];
    }
}
