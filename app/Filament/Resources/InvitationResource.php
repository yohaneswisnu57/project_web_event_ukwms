<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvitationResource\Pages;
use App\Filament\Resources\InvitationResource\RelationManagers;
use App\Models\Invitation;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Undangan';

    protected static ?string $modelLabel = 'Undangan';

    protected static ?string $pluralModelLabel = 'Undangan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Perusahaan / Alumni')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Masukkan nama perusahaan atau alumni'),

                TextInput::make('unique_code')
                    ->label('Kode Unik')
                    ->disabled()
                    ->dehydrated(true)
                    ->helperText('Kode unik akan di-generate otomatis saat disimpan.')
                    ->visibleOn('edit'),

                Toggle::make('is_used')
                    ->label('Sudah Mendaftar')
                    ->disabled()
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Perusahaan / Alumni')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unique_code')
                    ->label('Kode Unik')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kode berhasil disalin!'),

                Tables\Columns\TextColumn::make('registration_link')
                    ->label('Link Pendaftaran')
                    ->copyable()
                    ->copyMessage('Link berhasil disalin!')
                    ->limit(40),

                Tables\Columns\IconColumn::make('is_used')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('registration.attendance_count')
                    ->label('Jumlah Hadir')
                    ->default('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_used')
                    ->label('Status Pendaftaran')
                    ->trueLabel('Sudah Mendaftar')
                    ->falseLabel('Belum Mendaftar'),
            ])
            ->actions([
                Action::make('copyLink')
                    ->label('Copy Link')
                    ->icon('heroicon-o-clipboard')
                    ->color('info')
                    ->action(function (Invitation $record) {
                        Notification::make()
                            ->title('Link disalin!')
                            ->body($record->registration_link)
                            ->success()
                            ->send();
                    })
                    ->extraAttributes(fn (Invitation $record) => [
                        'x-on:click' => "navigator.clipboard.writeText('" . $record->registration_link . "')",
                    ]),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RegistrationRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvitations::route('/'),
            'create' => Pages\CreateInvitation::route('/create'),
            'edit' => Pages\EditInvitation::route('/{record}/edit'),
        ];
    }
}
