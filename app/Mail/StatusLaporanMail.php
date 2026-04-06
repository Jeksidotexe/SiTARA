<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusLaporanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;
    public User $user;
    public $url;
    public $catatan;

    public function __construct($laporan, User $user, string $url)
    {
        $this->laporan = $laporan;
        $this->user = $user;
        $this->url = $url;
        $this->catatan = $laporan->catatan;
    }

    public function envelope(): Envelope
    {
        $status = ucfirst($this->laporan->status_laporan);
        return new Envelope(
            subject: "SiTARA: Status Laporan $status",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.status-laporan',
            with: [
                'nama_operator' => $this->user->nama,
                'judul_laporan' => $this->laporan->judul,
                'status' => $this->laporan->status_laporan,
                'catatan' => $this->catatan,
                'url_laporan' => $this->url,
                'nama_pimpinan_verifikator' => $this->laporan->pimpinan->nama ?? 'Pimpinan',
            ],
        );
    }
}
