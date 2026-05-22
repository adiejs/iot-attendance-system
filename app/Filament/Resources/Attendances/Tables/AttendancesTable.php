<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                ->label('Photo')
                ->disk('public')
                ->circular()
                ->defaultImageUrl(function ($record) {
                    $name = $record && $record->mahasiswa ? $record->mahasiswa->nama : 'No Name';
                    $encodedName = urlencode($name);
                    return "https://ui-avatars.com/api/?name={$encodedName}&background=random&color=fff";
                }),
                TextColumn::make('mahasiswa.nama')
                    ->icon('heroicon-o-user')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->icon('heroicon-o-calendar')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('check_in')
                    ->icon('heroicon-o-clock')
                    ->time('H:i:s')
                    ->badge()
                    ->color('success'),
                TextColumn::make('check_out')
                    ->icon('heroicon-o-clock')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('H:i:s') : '-')
                    ->badge()
                    ->color(fn ($state) => $state ? 'warning' : 'gray'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // 3. Panggil class Filter langsung setelah di-import
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal'),
                        DatePicker::make('sampai_tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
