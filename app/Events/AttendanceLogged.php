<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceLogged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct()
    {
        // Tidak perlu mengirim data spesifik, cukup sinyal "ada absen baru"
    }

    public function broadcastOn(): Channel
    {
        return new Channel('attendance-channel');
    }

    public function broadcastAs(): string
    {
        return 'AttendanceLogged';
    }
}