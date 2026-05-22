<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\On;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Mendengarkan event AttendanceLogged dari channel 'attendance-channel'
     * menggunakan fitur Echo bawaan Livewire v3.
     */
    #[On('echo:attendance-channel,.AttendanceLogged')]
    public function refreshTable(): void
    {
        // Memicu untuk me-render ulang tabel
        $this->dispatch('$refresh');
    }
}
