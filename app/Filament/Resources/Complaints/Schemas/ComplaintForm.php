<?php

namespace App\Filament\Resources\Complaints\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;

class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Complaint Information')
                    ->schema([
                        Placeholder::make('student_details')
                            ->label('Student')
                            ->content(fn ($record) => $record?->student?->student_name . ' (' . $record?->student?->student_id . ')'),
                        
                        Select::make('category')
                            ->options([
                                'student_name' => 'Student Name',
                                'father_name' => 'Father Name',
                                'date_of_birth' => 'Date of Birth',
                                'cnic' => 'CNIC',
                                'mobile' => 'Mobile Number',
                                'email' => 'Email Address',
                                'domicile' => 'Domicile',
                                'exam_center' => 'Exam Center',
                                'exam_fee' => 'Exam Fee',
                                'picture' => 'Profile Picture / Image Correction',
                                'roll_number' => 'Roll Number',
                                'other' => 'Other Information',
                            ])
                            ->required(),
                        
                        Placeholder::make('current_value')
                            ->label('Current Record Value')
                            ->content(function ($record) {
                                if (!$record || !$record->student) return '-';
                                
                                if ($record->category === 'picture') {
                                    $url = $record->student->picture_path . '?v=' . time();
                                    return new \Illuminate\Support\HtmlString("<img src='{$url}' style='height: 100px; border-radius: 8px;' alt='Current Profile Picture'/>");
                                }
                                
                                $fieldMap = [
                                    'student_name' => $record->student->student_name,
                                    'father_name' => $record->student->father_name,
                                    'date_of_birth' => $record->student->date_of_birth,
                                    'cnic' => $record->student->id_number ?? $record->student->cnic,
                                    'mobile' => $record->student->mobile,
                                    'email' => $record->student->email,
                                    'domicile' => $record->student->domicile,
                                    'exam_center' => $record->student->centre,
                                    'exam_fee' => $record->student->exam_fee,
                                    'roll_number' => $record->student->sorted_roll_number_uhs ?? $record->student->roll_number,
                                ];
                                
                                $val = $fieldMap[$record->category] ?? '-';
                                return filled($val) ? $val : '-';
                            }),
                        
                        Textarea::make('problem_details')
                            ->required()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\FileUpload::make('attachment_path')
                            ->label('Attached Image')
                            ->image()
                            ->disk('public')
                            ->disabled()
                            ->dehydrated(false)
                            ->downloadable()
                            ->hidden(fn ($record) => !$record || !$record->attachment_path)
                            ->columnSpanFull(),
                            
                        \Filament\Schemas\Components\Actions::make([
                            \Filament\Actions\Action::make('apply_image_correction')
                                ->label('Apply Image to Student Profile')
                                ->icon('heroicon-m-check-badge')
                                ->color('success')
                                ->visible(fn ($record) => $record && $record->category === 'picture' && $record->attachment_path && $record->status !== 'solved')
                                ->requiresConfirmation()
                                ->modalHeading('Apply Image Correction')
                                ->modalDescription('Are you sure you want to replace the student\'s profile picture with this attached image? This will automatically mark the complaint as solved.')
                                ->action(function ($record, $set) {
                                    if ($record && $record->student) {
                                        $student = $record->student;
                                        
                                        $newFileName = "mdcatimages/{$student->student_id}.jpg";
                                        
                                        $oldPath = $student->getRawOriginal('picture_path');
                                        if ($oldPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                                            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                                        }
                                        
                                        \Illuminate\Support\Facades\Storage::disk('public')->copy($record->attachment_path, $newFileName);
                                        
                                        $student->picture_path = $newFileName;
                                        $student->save();
                                        
                                        $set('status', 'solved');
                                        $set('admin_remarks', 'Image correction applied successfully directly from the complaint panel.');
                                        
                                        \Filament\Notifications\Notification::make()
                                            ->title('Image Applied!')
                                            ->body('The student\'s profile picture has been permanently updated.')
                                            ->success()
                                            ->send();
                                    }
                                }),
                            
                            \Filament\Actions\Action::make('apply_text_correction')
                                ->label('Apply Text Correction')
                                ->icon('heroicon-m-pencil-square')
                                ->color('warning')
                                ->visible(fn ($record) => $record && $record->category !== 'picture' && $record->status !== 'solved')
                                ->form(function ($record) {
                                    $currentVal = '';
                                    if ($record && $record->student) {
                                        $fieldMap = [
                                            'student_name' => $record->student->student_name,
                                            'father_name' => $record->student->father_name,
                                            'date_of_birth' => $record->student->date_of_birth,
                                            'cnic' => $record->student->id_number ?? $record->student->cnic,
                                            'mobile' => $record->student->mobile,
                                            'email' => $record->student->email,
                                            'domicile' => $record->student->domicile,
                                            'exam_center' => $record->student->centre,
                                            'exam_fee' => $record->student->exam_fee,
                                            'roll_number' => $record->student->sorted_roll_number_uhs ?? $record->student->roll_number,
                                        ];
                                        $currentVal = $fieldMap[$record->category] ?? '';
                                    }
                                    
                                    return [
                                        \Filament\Forms\Components\TextInput::make('new_value')
                                            ->label('Corrected Value for ' . ucwords(str_replace('_', ' ', $record->category ?? '')))
                                            ->default($currentVal)
                                            ->required(),
                                    ];
                                })
                                ->action(function ($record, array $data, $set) {
                                    if ($record && $record->student) {
                                        $student = $record->student;
                                        $cat = $record->category;

                                        if ($cat === 'cnic') {
                                            $student->id_number = $data['new_value'];
                                        } elseif ($cat === 'exam_center') {
                                            $student->centre = $data['new_value'];
                                        } elseif ($cat === 'roll_number') {
                                            $student->roll_number = $data['new_value'];
                                        } else {
                                            $student->{$cat} = $data['new_value'];
                                        }
                                        
                                        $student->save();
                                        
                                        $set('status', 'solved');
                                        $set('admin_remarks', 'Correction applied successfully: ' . $data['new_value']);
                                        
                                        \Filament\Notifications\Notification::make()
                                            ->title('Correction Applied!')
                                            ->body('The student\'s record has been updated.')
                                            ->success()
                                            ->send();
                                    }
                                }),
                        ])->columnSpanFull(),
                    ])->columns(2),

                Section::make('Admin Action')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'In Progress',
                                'solved' => 'Solved',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('pending'),
                        
                        Textarea::make('admin_remarks')
                            ->columnSpanFull(),
                        
                        DateTimePicker::make('solved_at')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(1),
            ]);
    }
}
