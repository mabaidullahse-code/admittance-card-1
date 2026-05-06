<?php
// app/Jobs/GenerateAdmitCardJob.php

namespace App\Jobs;

use App\Models\Student;
use App\Models\AdmitCard;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Log;

class GenerateAdmitCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $studentId;
    protected $userId;
    public $timeout = 300; 
    public $tries = 3;

    public function __construct($studentId, $userId = null)
    {
        $this->studentId = $studentId;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            $student = Student::findOrFail($this->studentId);
            $fileName = "admit_cards/Admit_Card_{$student->student_id}.pdf";
            $fullPath = storage_path("app/public/{$fileName}");

            // Ensure directory exists
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            
            // Generate and Save PDF using Spatie PDF (Browsershot)
            Pdf::view('pdf.admit-card', ['student' => $student])
                ->format('a4')
                ->disk('public')
                ->save($fileName);
            
            // Create admit card record
            AdmitCard::create([
                'student_id' => $student->id,
                'file_path' => $fileName,
                'generated_by' => $this->userId ?? 'background_job',
                'generated_at' => now(),
                'download_count' => 0,
            ]);
            
            Log::info("Admit card generated successfully via job for student: {$student->student_id}");
            
        } catch (\Exception $e) {
            Log::error("Failed to generate admit card for student {$this->studentId}: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function failed(\Throwable $exception)
    {
        Log::error("Admit card generation job failed for student {$this->studentId}: " . $exception->getMessage());
    }
}