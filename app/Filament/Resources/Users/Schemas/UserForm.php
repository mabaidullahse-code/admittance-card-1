<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        if (filled($state)) {
                            $set('plain_password', $state);
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                TextInput::make('plain_password')
                    ->label('Plain Password (Stored)')
                    ->helperText('This password will be stored in plain text in the database as requested.')
                    ->disabled()
                    ->dehydrated(),
                \Filament\Forms\Components\CheckboxList::make('permissions')
                    ->options(\App\Models\User::PERMISSIONS)
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(fn () => auth()->id() === 1 || (auth()->user() && auth()->user()->hasPermission('users'))),
            ]);
    }
}
