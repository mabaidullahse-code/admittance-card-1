<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student_id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('father_name')
                    ->searchable(),
                TextColumn::make('roll_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sorted_roll_number_uhs')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cnic')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('centre')
                    ->searchable(),
                TextColumn::make('exam_name')
                    ->searchable(),
                IconColumn::make('paid')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                \Filament\Actions\ImportAction::make()
                    ->importer(\App\Filament\Imports\StudentImporter::class)
                    ->fileRules('max:51200')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
