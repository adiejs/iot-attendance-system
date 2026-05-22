<?php

namespace App\Filament\Resources\Mahasiswas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MahasiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nim')
                    ->label('ID')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('nama')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('rfid_uid')
                    ->label('UID CARD / RFID')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    // Nanti kita akan tambahkan action/button khusus di sini untuk scan real-time
                    ->helperText('Biarkan kosong jika belum ada KTP yang didaftarkan.'),
            ]);
    }
}
