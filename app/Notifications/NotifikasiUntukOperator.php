<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NotifikasiUntukOperator extends Notification implements ShouldQueue
{
    use Queueable;

    public $laporan;
    public $message;
    public $url;

    public function __construct($laporan, string $message, string $url)
    {
        $this->laporan = $laporan;
        $this->message = $message;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'url' => $this->url,
            'laporan_id' => $this->laporan->id_laporan,
            'notification_id' => $this->id
        ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'laporan_id' => $this->laporan->id_laporan,
        ];
    }
}
