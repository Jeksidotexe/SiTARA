<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NotifikasiBackup extends Notification implements ShouldQueue
{
    use Queueable;

    public $status; // 'success' atau 'error'
    public $message;

    public function __construct($status, $message)
    {
        $this->status = $status;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        // Kirim ke database (lonceng) dan broadcast (toast real-time)
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'url' => route('backup.index'), // Arahkan ke halaman backup
            'type' => 'backup_status'
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'url' => route('backup.index'),
            'notification_id' => $this->id,
            'status' => $this->status, // Mengirim status untuk warna toast (danger/success)
        ]);
    }
}
