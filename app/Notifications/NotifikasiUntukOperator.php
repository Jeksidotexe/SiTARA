<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Channels\WhatsAppChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\StatusLaporanMail;
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

    // 1. Aktifkan channel
    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail', WhatsAppChannel::class];
    }

    // 2. Format WhatsApp
    public function toWhatsapp($notifiable)
    {
        $status = $this->laporan->status_laporan;
        $emoji = ($status == 'disetujui') ? '✅' : '⚠️';
        $title = ($status == 'disetujui') ? 'Laporan Disetujui' : 'Perlu Revisi';

        $msg = "*[SiTARA] $title $emoji*\n\n" .
            "Halo " . $notifiable->nama . ",\n" .
            $this->message;

        if ($status == 'revisi' && !empty($this->laporan->catatan)) {
            $msg .= "\n\n*Catatan Pimpinan:*\n" .
                "_" . $this->laporan->catatan . "_";
        }

        $msg .= "\n\nSilakan cek di: " . $this->url;

        return $msg;
    }

    // 3. Format Email
    public function toMail($notifiable)
    {
        // Mengembalikan instance Mailable
        return (new StatusLaporanMail($this->laporan, $notifiable, $this->url))
            ->to($notifiable->email);
    }

    // ... (Biarkan toBroadcast dan toDatabase tetap sama)
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
