<?php

namespace App\Filament\Resources\Mahasiswas\Pages;

use App\Filament\Resources\Mahasiswas\MahasiswaResource;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\On;
use App\Models\Attendance;

class CreateMahasiswa extends CreateRecord
{

    protected static string $resource = MahasiswaResource::class;

    public function mount(): void
    {
        parent::mount();
        // Ambil data absensi (attend) terakhir berdasarkan waktu dibuat
        $lastAttendance = Attendance::whereNotNull('rfid_uid')
            ->latest('created_at')
            ->value('rfid_uid');

        // Cek apakah data ada, lalu isi ke dalam data form rfid_uid
        if ($lastAttendance) {
            $this->data['rfid_uid'] = $lastAttendance;
        }
    }

    // Mendengarkan event UidScanned dari Reverb
    #[On('echo:rfid-channel,UidScanned')]
    public function updateRfidUid(array $event)
    {
        // Mengisi input 'rfid_uid' di form secara otomatis dengan UID dari ESP32
        $this->data['rfid_uid'] = $event['uid'];
    }
}
