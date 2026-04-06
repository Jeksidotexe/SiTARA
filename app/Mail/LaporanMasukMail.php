<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LaporanMasukMail extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;
    public User $user;
    public $url;

    public function __construct($laporan, User $user, string $url)
    {
        $this->laporan = $laporan;
        $this->user = $user;
        $this->url = $url;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SiTARA: Laporan Baru Masuk',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.laporan-masuk',
            with: [
                'nama_pimpinan' => $this->user->nama,
                'judul_laporan' => $this->laporan->judul,
                'nama_operator' => $this->laporan->operator->nama ?? 'N/A',
                'tanggal' => $this->laporan->tanggal_laporan ? $this->laporan->tanggal_laporan->isoFormat('D MMMM YYYY') : '-',
                'url_laporan' => $this->url,
            ],
        );
    }
}
