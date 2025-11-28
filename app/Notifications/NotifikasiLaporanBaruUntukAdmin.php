<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NotifikasiLaporanBaruUntukAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public $laporan;
    public $jenisLaporan;
    public $namaWilayah;
    public $url;
    public $message;

    public function __construct($laporan, $jenisLaporan, $namaWilayah, $url)
    {
        $this->laporan = $laporan;
        $this->jenisLaporan = $jenisLaporan;
        $this->namaWilayah = $namaWilayah;
        $this->url = $url;

        // Format Pesan: "Laporan [Jenis] dari [Wilayah] telah terverifikasi."
        $this->message = "Laporan Masuk: {$this->jenisLaporan} dari {$this->namaWilayah}";
    }

    public function via($notifiable)
    {
        // Kirim ke Database (untuk lonceng notifikasi) dan Broadcast (untuk popup real-time)
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'url' => $this->url,
            'laporan_id' => $this->laporan->id_laporan,
            'notification_id' => $this->id,
            'type' => 'admin_report_entry' // Penanda khusus untuk frontend
        ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'laporan_id' => $this->laporan->id_laporan,
            'type' => 'admin_report_entry'
        ];
    }
}
