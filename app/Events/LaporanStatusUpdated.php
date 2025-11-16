<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LaporanStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * ID laporan yang diupdate.
     * @var int
     */
    public $laporanId;

    /**
     * Status baru dari laporan.
     * @var string
     */
    public $newStatus;

    /**
     * Buat instance event baru.
     *
     * @param int $laporanId
     * @param string $newStatus
     */
    public function __construct(int $laporanId, string $newStatus)
    {
        $this->laporanId = $laporanId;
        $this->newStatus = $newStatus;
    }

    /**
     * Tentukan channel publik.
     */
    public function broadcastOn(): array
    {
        // Tetap di channel publik yang sama
        return [new Channel('laporan-updates')];
    }

    /**
     * Tentukan nama event kustom.
     */
    public function broadcastAs(): string
    {
        return 'LaporanUpdated';
    }

    /**
     * Data yang akan di-broadcast.
     * Kita kirim ID dan status baru ke listener.
     */
    public function broadcastWith(): array
    {
        return [
            'laporanId' => $this->laporanId,
            'newStatus' => $this->newStatus,
        ];
    }
}
