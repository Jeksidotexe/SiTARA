<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Variabel publik akan otomatis tersedia di view
     */
    public User $user;
    public string $token;

    /**
     * Buat instance pesan baru.
     *
     * @param \App\Models\User $user
     * @param string $token
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Dapatkan "amplop" pesan (Subject/Subjek).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Instruksi Reset Password Akun Anda',
        );
    }

    /**
     * Dapatkan konten pesan (View).
     * Ini adalah file template Blade yang akan kita buat di Langkah 3.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
            with: [
                'nama_user' => $this->user->nama,
                'url_reset' => route('password.reset', [
                    'token' => $this->token,
                    'email' => $this->user->email,
                ]),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
