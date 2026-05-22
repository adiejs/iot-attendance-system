<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Implementasikan ShouldBroadcastNow agar langsung dikirim tanpa delay (queue)
class UidScanned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $uid;

    public function __construct(string $uid)
    {
        $this->uid = $uid;
    }

    // Nama channel WebSocket
    public function broadcastOn(): Channel
    {
        return new Channel('rfid-channel');
    }
}