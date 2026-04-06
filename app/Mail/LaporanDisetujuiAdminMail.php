<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LaporanDisetujuiAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;
    public User $admin;
    public $url;

    public function __construct($laporan, User $admin, string $url)
    {
        $this->laporan = $laporan;
        $this->admin = $admin;
        $this->url = $url;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SiMANTEL: Laporan Terverifikasi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.laporan-disetujui-admin',
            with: [
                'nama_admin' => $this->admin->nama,
                'judul_laporan' => $this->laporan->judul,
                'nama_operator' => $this->laporan->operator->nama ?? 'N/A',
                'nama_wilayah' => $this->laporan->operator->wilayah->nama_wilayah ?? 'N/A',
                'nama_pimpinan' => $this->laporan->pimpinan->nama ?? 'Pimpinan',
                'tanggal' => $this->laporan->tanggal_laporan ? $this->laporan->tanggal_laporan->isoFormat('D MMMM YYYY') : '-',
                'url_laporan' => $this->url,
            ],
        );
    }
}
