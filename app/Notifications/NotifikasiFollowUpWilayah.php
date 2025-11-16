<?php

namespace App\Notifications;

use App\Models\Wilayah; // <-- Penting
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NotifikasiFollowUpWilayah extends Notification implements ShouldQueue
{
    use Queueable;

    public $wilayah;
    public $message;
    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct(Wilayah $wilayah)
    {
        $this->wilayah = $wilayah;
        $this->message = "Status Wilayah '{$this->wilayah->nama_wilayah}' masih '{$this->wilayah->status_wilayah}'. Mohon tindak lanjut.";

        $this->url = route('dashboard', ['follow_up' => $this->wilayah->id_wilayah]);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Kirim ke database (untuk daftar notif) dan broadcast (untuk real-time)
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'wilayah_id' => $this->wilayah->id_wilayah, // <-- Data pelacakan
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'url' => $this->url,
            'notification_id' => $this->id,
        ]);
    }
}
