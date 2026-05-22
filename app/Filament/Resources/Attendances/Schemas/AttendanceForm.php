<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('foto')
                    ->label('Photo')
                    ->image()
                    ->disk('public') // --- TAMBAHKAN INI ---
                    ->directory('attendances') // --- TAMBAHKAN INI JUGA ---
                    ->disabled() 
                    ->columnSpanFull(),
                Select::make('mahasiswa_id')
                    ->label('Name')
                    ->disabled()
                    ->relationship('mahasiswa', 'nama')
                    ->required(),
                DatePicker::make('tanggal')
                    ->label('Date')
                    ->required(),
                DateTimePicker::make('check_in'),
                DateTimePicker::make('check_out'),
            ]);
    }
}
