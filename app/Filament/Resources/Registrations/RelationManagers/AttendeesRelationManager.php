<?php

namespace App\Filament\Resources\Registrations\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AttendeesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendees';

    protected static ?string $title = 'Daftar Tamu';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Tamu')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('email')
                    ->label('Email')
                    ->email(),
                
                TextInput::make('phone')
                    ->label('Nomor HP')
                    ->tel(),
                
                TextInput::make('position')
                    ->label('Jabatan')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tamu')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
                
                Tables\Columns\TextColumn::make('phone')
                    ->label('Nomor HP'),
                
                Tables\Columns\TextColumn::make('position')
                    ->label('Jabatan'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
