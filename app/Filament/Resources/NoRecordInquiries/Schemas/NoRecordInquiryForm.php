<?php

namespace App\Filament\Resources\NoRecordInquiries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class NoRecordInquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('candidate_id')
                    ->required(),
                TextInput::make('student_name')
                    ->required(),
                TextInput::make('father_name')
                    ->required(),
                TextInput::make('cnic')
                    ->required()
                    ->label('CNIC Number'),
                \Filament\Forms\Components\Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])
                    ->required(),
                \Filament\Forms\Components\DatePicker::make('dob')
                    ->label('Date of Birth')
                    ->required(),
                TextInput::make('mobile')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('ip_address')
                    ->disabled()
                    ->dehydrated(false),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'resolved' => 'Resolved',
                    ])
                    ->required()
                    ->default('pending'),
                Textarea::make('admin_notes')
                    ->columnSpanFull(),
            ]);
    }
}
