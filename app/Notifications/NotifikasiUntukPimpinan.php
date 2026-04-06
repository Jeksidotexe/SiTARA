<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Channels\WhatsAppChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\LaporanMasukMail;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NotifikasiUntukPimpinan extends Notification implements ShouldQueue
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

    // 1. Tambahkan 'mail' dan WhatsAppChannel::class di sini
    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail', WhatsAppChannel::class];
    }

    // 2. Format Pesan WhatsApp
    public function toWhatsapp($notifiable)
    {
        return "*[SiTARA] Laporan Masuk*\n\n" .
            "Yth. " . $notifiable->nama . ",\n" .
            $this->message . "\n\n" .
            "*Detail Singkat:*\n" .
            "• Judul: " . $this->laporan->judul . "\n" .
            "• Operator: " . ($this->laporan->operator->nama ?? 'N/A') . "\n\n" .
            "Mohon segera ditindaklanjuti melalui link berikut:\n" .
            $this->url;
    }

    // 3. Format Email
    public function toMail($notifiable)
    {
        // Mengembalikan instance Mailable
        return (new LaporanMasukMail($this->laporan, $notifiable, $this->url))
            ->to($notifiable->email);
    }

    // ... (Biarkan method toBroadcast dan toDatabase tetap sama seperti sebelumnya)
    public function toBroadcast($notifiable)
    {
        $this->laporan->loadMissing('operator');
        return new BroadcastMessage([
            'message' => $this->message,
            'url' => $this->url,
            'laporan_id' => $this->laporan->id_laporan,
            'judul' => $this->laporan->judul,
            'operator_name' => $this->laporan->operator->nama ?? 'N/A',
            'tanggal_laporan_human' => $this->laporan->tanggal_laporan ? $this->laporan->tanggal_laporan->isoFormat('D MMMM YYYY') : 'N/A',
            'created_at_human' => 'Baru saja',
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
