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
            ->view('emails.orden_pagada', ['orden' => $this->orden]);
    }

    public function toArray($notifiable)
    {

        return [
            'orden_id' => $this->orden->id,
            'estado' => 'Pagado',
            'mensaje' => "¡Gracias por tu compra! Tu orden #{$this->orden->id} ha sido confirmada y está en proceso de producción. Te notificaremos cuando esté lista para el envío."

        ];
    }
}
