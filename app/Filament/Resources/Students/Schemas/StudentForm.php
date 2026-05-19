<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Student Profile Picture')
                    ->description('View and update the student profile photo.')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        Placeholder::make('current_image')
                            ->label('Current Image')
                            ->content(function ($record) {
                                if (!$record) return 'No image available';
                                $path = $record->picture_path . '?v=' . time();
                                return new HtmlString('<img src="' . $path . '" class="w-18 h-18 rounded-xl border-2 border-gray-200 shadow-md object-cover bg-white" onerror="this.src=\'https://ui-avatars.com/api/?name=' . urlencode($record->student_name) . '&color=7F9CF5&background=EBF4FF\'" />');
                            }),
                        FileUpload::make('picture_path')
                            ->label('Update Picture')
                            ->image()
                            ->disk('public')
                            ->directory('mdcatimages')
                            ->visibility('public')
                            ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, callable $get) {
                                $studentId = $get('student_id');
                                return $studentId ? "{$studentId}.jpg" : $file->getClientOriginalName();
                            })
                            ->deleteUploadedFileUsing(function ($file, $record) {
                                // Explicitly delete the old file from the public disk
                                if ($file) {
                                    \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                                }
                            })
                            ->imageEditor()
                            ->circleCropper(),
                    ]),
                Section::make('Personal Information')
                    ->description('Core identification and contact details.')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('student_id')
                            ->required()
                            ->label('Candidate ID'),
                        TextInput::make('student_name')
                            ->required(),
                        TextInput::make('father_name'),
                        TextInput::make('gender'),
                        TextInput::make('marital_status'),
                        TextInput::make('id_type')
                            ->placeholder('CNIC / B-Form'),
                        TextInput::make('id_number'),
                        DatePicker::make('date_of_birth'),
                        TextInput::make('mobile'),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email(),
                    ]),
                Section::make('Exam & Download Details')
                    ->description('Tracking admit card status and exam fee.')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('sorted_roll_number_uhs')
                            ->label('UHS Roll Number'),
                        TextInput::make('roll_number')
                            ->label('Portal Roll Number'),
                        TextInput::make('exam_name'),
                        TextInput::make('exam_fee')
                            ->numeric()
                            ->prefix('Rs.'),
                        TextInput::make('ap_isdownload')
                            ->label('Download Count')
                            ->disabled()
                            ->numeric(),
                        Toggle::make('paid')
                            ->label('Fee Paid Status'),
                        DateTimePicker::make('paid_at'),
                    ]),
                Section::make('Location & Domicile')
                    ->description('Candidate geographical information.')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('city'),
                        TextInput::make('province'),
                        TextInput::make('domicile'),
                        TextInput::make('nationality'),
                        TextInput::make('national_center_name')
                            ->label('National Center Name'),
                        \Filament\Forms\Components\Textarea::make('centre')
                            ->label('Full Center Address')
                            ->columnSpanFull(),
                    ]),
                Section::make('Family & Background')
                    ->description('Additional candidate background details.')
                    ->collapsible()
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        TextInput::make('father_profession'),
                        TextInput::make('mother_profession'),
                        TextInput::make('bank_name'),
                        TextInput::make('bank_account_title'),
                        TextInput::make('iban'),
                    ]),
            ]);
    }
}
