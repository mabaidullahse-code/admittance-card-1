<?php

namespace App\Filament\Resources\Complaints\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;

class ComplaintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.student_name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.student_id')
                    ->label('Student ID')
                    ->searchable(),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'student_name' => 'info',
                        'cnic' => 'warning',
                        'picture' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('problem_details')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->problem_details),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'solved' => 'success',
                        'rejected' => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'solved' => 'Solved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('category')
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
                    ]),
            ])
            ->actions([
                EditAction::make(),
                Action::make('markAsSolved')
                    ->label('Solve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn ($record) => $record->update([
                        'status' => 'solved',
                        'solved_at' => now(),
                    ]))
                    ->visible(fn ($record) => $record->status !== 'solved'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
