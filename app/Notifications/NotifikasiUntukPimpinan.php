<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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

    // Kita kirim via 'database' (untuk persistensi) dan 'broadcast' (untuk Reverb)
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    // Data yang dikirim ke Reverb
    public function toBroadcast($notifiable)
    {
        // Muat relasi operator jika belum ada
        $this->laporan->loadMissing('operator');

        return new BroadcastMessage([
            // Data untuk Navbar/Toast
            'message' => $this->message,
            'url' => $this->url,

            // [MODIFIKASI] Data untuk real-time update tabel
            'laporan_id' => $this->laporan->id_laporan,
            'judul' => $this->laporan->judul, // <-- TAMBAHKAN INI
            'operator_name' => $this->laporan->operator->nama ?? 'N/A',
            'tanggal_laporan_human' => $this->laporan->tanggal_laporan ? $this->laporan->tanggal_laporan->isoFormat('D MMMM YYYY') : 'N/A',
            'created_at_human' => 'Baru saja',

            'notification_id' => $this->id
        ]);
    }

    // Data yang disimpan ke tabel 'notifications'
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'laporan_id' => $this->laporan->id_laporan,
        ];
    }
}
