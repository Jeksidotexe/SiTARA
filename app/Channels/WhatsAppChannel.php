<?php

namespace App\Channels;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, $notification)
    {
        if (!method_exists($notification, 'toWhatsapp')) {
            return;
        }

        $message = $notification->toWhatsapp($notifiable);

        $to = $notifiable->no_telepon;

        if (empty($to)) {
            $userId = $notifiable->id_users ?? $notifiable->id ?? 'Unknown';
            Log::warning("Gagal mengirim pesan: No Telepon user {$userId} kosong.");
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN'),
            ])->post('https://api.fonnte.com/send', [
                'target' => $to,
                'message' => $message,
                'countryCode' => '62', // Otomatis ubah 08xx jadi 628xx
            ]);

            if ($response->failed()) {
                Log::error('Fonnte API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp Channel Exception: " . $e->getMessage());
        }
    }
}
