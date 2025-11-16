<?php

namespace App\Events;

use App\Models\Wilayah;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WilayahUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // [TAMBAHKAN] Properti publik untuk data yang akan dikirim
    public Wilayah $wilayah;
    public string $action; // 'created', 'updated', 'deleted'

    /**
     * Create a new event instance.
     */
    // [MODIFIKASI] Ubah constructor untuk menerima data
    public function __construct(Wilayah $wilayah, string $action = 'updated')
    {
        $this->wilayah = $wilayah;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Menggunakan channel publik karena ini adalah data non-sensitif
        // dan hanya bisa diakses oleh Admin.
        return [
            new Channel('wilayah-updates'),
        ];
    }

    /**
     * Nama event yang akan didengarkan di JavaScript.
     * Defaultnya adalah 'WilayahUpdated', kita ubah agar konsisten.
     */
    public function broadcastAs()
    {
        return 'WilayahUpdated'; // Listener akan mendengarkan '.WilayahUpdated'
    }
}
