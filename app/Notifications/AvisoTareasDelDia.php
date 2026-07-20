<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class AvisoTareasDelDia extends Notification
{
    use Queueable;

    public function __construct(private readonly int $totalTareas)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $canales = ['mail'];

        if ($notifiable->pushSubscriptions()->exists()) {
            $canales[] = WebPushChannel::class;
        }

        return $canales;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tus tareas de hoy — Sensilimp')
            ->greeting('¡Buenos días, '.explode(' ', $notifiable->name)[0].'!')
            ->line($this->totalTareas === 1
                ? 'Hoy tienes 1 tarea asignada.'
                : "Hoy tienes {$this->totalTareas} tareas asignadas.")
            ->action('Ver mis tareas', url('/operario/tareas'))
            ->line('Que tengas un buen día.');
    }

    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Tus tareas de hoy')
            ->icon('/icons/icon-192.png')
            ->body($this->totalTareas === 1
                ? 'Tienes 1 tarea asignada para hoy.'
                : "Tienes {$this->totalTareas} tareas asignadas para hoy.")
            ->data(['url' => '/operario/tareas'])
            ->tag('aviso-tareas-del-dia');
    }
}
