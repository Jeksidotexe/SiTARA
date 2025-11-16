<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Wilayah;
use App\Notifications\NotifikasiFollowUpWilayah;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckWilayahStatus extends Command
{
    /**
     * Signature command
     */
    protected $signature = 'simantel:check-wilayah-status';

    /**
     * Deskripsi command
     */
    protected $description = 'Periksa status wilayah (Siaga/Bahaya) dan kirim notifikasi follow-up ke pimpinan setelah 24 jam.';

    /**
     * Jalankan logic command
     */
    public function handle()
    {
        Log::info('Cron [CheckWilayahStatus]: Memulai pengecekan status wilayah...');

        // 1. Tentukan batas waktu (24 jam yang lalu)
        $threshold = Carbon::now()->subHours(24);

        // 2. Cari semua wilayah yang statusnya 'Siaga' atau 'Bahaya'
        $problemWilayahs = Wilayah::whereIn('status_wilayah', ['Siaga', 'Bahaya'])
            ->whereNotNull('status_wilayah_updated_at') // Pastikan ada timestamp
            ->get();

        $this->info("Ditemukan {$problemWilayahs->count()} wilayah berstatus Siaga/Bahaya.");
        $notifCount = 0;

        foreach ($problemWilayahs as $wilayah) {

            // 3. Cek Timestamp: Apakah status ini di-set LEBIH DARI 24 jam lalu?
            if ($wilayah->status_wilayah_updated_at > $threshold) {
                // Status diubah < 24 jam lalu. Masih aman, lewati.
                Log::info("Wilayah [{$wilayah->nama_wilayah}]: Status baru (< 24 jam). Skip.");
                continue;
            }

            // --- Jika lolos (status sudah > 24 jam), cari Pimpinan ---

            // 4. Asumsi: Pimpinan adalah user 'pimpinan' yang punya id_wilayah sama
            $pimpinan = User::where('role', 'pimpinan')
                ->where('id_wilayah', $wilayah->id_wilayah)
                ->first();

            if (!$pimpinan) {
                Log::warning("Cron [CheckWilayahStatus]: Tidak ditemukan pimpinan untuk Wilayah ID {$wilayah->id_wilayah}.");
                continue;
            }

            // 5. Logika Anti-Spam: Cek notifikasi terakhir
            $lastNotif = $pimpinan->notifications()
                ->where('type', NotifikasiFollowUpWilayah::class) // Notif Tipe FollowUp
                ->where('data->wilayah_id', $wilayah->id_wilayah) // Untuk Wilayah ini
                ->latest()
                ->first();

            $sendNotif = false;

            if (!$lastNotif) {
                // BELUM PERNAH ada notif. Ini notif 24 jam pertama.
                $sendNotif = true;
                Log::info("Wilayah [{$wilayah->nama_wilayah}]: Kirim notif 24 jam pertama.");
            } else {
                // SUDAH ADA notif. Cek kapan dikirim.
                // Jika notif terakhir dikirim LEBIH DARI 24 jam lalu
                if ($lastNotif->created_at <= $threshold) {
                    $sendNotif = true; // Kirim notif follow-up (48 jam, 72 jam, dst.)
                    Log::info("Wilayah [{$wilayah->nama_wilayah}]: Notif terakhir > 24 jam. Kirim follow-up.");
                } else {
                    // Notif terakhir < 24 jam lalu. Jangan spam.
                    Log::info("Wilayah [{$wilayah->nama_wilayah}]: Notif terakhir < 24 jam. Skip.");
                }
            }

            // 6. Kirim Notifikasi
            if ($sendNotif) {
                $pimpinan->notify(new NotifikasiFollowUpWilayah($wilayah));
                $notifCount++;
            }
        }

        Log::info("Cron [CheckWilayahStatus]: Selesai. {$notifCount} notifikasi terkirim.");
        $this->info("{$notifCount} notifikasi follow-up terkirim.");
        return 0;
    }
}
