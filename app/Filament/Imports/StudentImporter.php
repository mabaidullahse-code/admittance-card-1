<?php

namespace App\Filament\Imports;

use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('student_id')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('student_name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('father_name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('gender')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('marital_status')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('mobile')
                ->rules(['nullable', 'max:20']),
            ImportColumn::make('cnic')
                ->rules(['nullable', 'max:20']),
            ImportColumn::make('id_type')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('id_number')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('date_of_birth')
                ->rules(['nullable']),
            ImportColumn::make('sorted_roll_number_uhs')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('roll_number')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('domicile')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('province')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('city')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('nationality')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('father_profession')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('mother_profession')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('exam_name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('exam_fee')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('paid')
                ->boolean()
                ->rules(['nullable', 'boolean']),
            ImportColumn::make('paid_at')
                ->rules(['nullable']),
            ImportColumn::make('national_center')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('exam_preferred_date')
                ->rules(['nullable']),
            ImportColumn::make('national_center_name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('international_center_name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('national_other_center_name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('international_other_center_name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('bank_account_title')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('bank_name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('iban')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('contact_no')
                ->rules(['nullable', 'max:20']),
            ImportColumn::make('profile_picture')
                ->rules(['nullable', 'max:500']),
            ImportColumn::make('centre')
                ->rules(['nullable', 'max:500']),
            ImportColumn::make('picture_path')
                ->rules(['nullable', 'max:500']),
            ImportColumn::make('ap_isdownload')
                ->rules(['nullable', 'max:10']),
        ];
    }

    public function resolveRecord(): ?Student
    {
        // Clean and sanitize all data
        foreach ($this->data as $key => $value) {
            if (trim((string)$value) === '' || strtoupper(trim((string)$value)) === 'NULL') {
                $this->data[$key] = null;
                continue;
            }

            // General string sanitization (trim and remove double spaces)
            $cleaned = trim((string)$value);
            $cleaned = preg_replace('/\s+/', ' ', $cleaned);

            // Special handling for CNIC (remove dashes and all spaces)
            if ($key === 'cnic') {
                $cleaned = str_replace(['-', ' '], '', $cleaned);
            }

            $this->data[$key] = $cleaned;
        }

        $studentId = $this->data['student_id'] ?? null;
        
        if (!$studentId) {
            return null;
        }

        return Student::firstOrNew([
            'student_id' => $studentId,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your student import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
