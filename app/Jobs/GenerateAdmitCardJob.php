<?php
// app/Jobs/GenerateAdmitCardJob.php

namespace App\Jobs;

use App\Models\Student;
use App\Models\AdmitCard;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class GenerateAdmitCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $studentId;
    protected $userId;
    public $timeout = 120;
    public $tries = 3;

    public function __construct($studentId, $userId = null)
    {
        $this->studentId = $studentId;
        $this->userId = $userId;
    }

    public function handle()
    {
        $lockKey = "admit_card_generation_{$this->studentId}";
        $lock = Redis::lock($lockKey, 60);
        
        if (!$lock->get()) {
            Log::warning("Admit card generation already in progress for student: {$this->studentId}");
            return;
        }
        
        try {
            $student = Student::findOrFail($this->studentId);
            
            // Generate PDF
            $pdf = Pdf::loadView('pdf.admit-card', ['student' => $student]);
            $pdf->setPaper('a4', 'portrait');
            
            $fileName = "admit_cards/admit_card_{$student->student_id}_{$student->id}.pdf";
            Storage::disk('public')->put($fileName, $pdf->output());
            
            // Update student record
            $student->update(['admit_card_path' => $fileName]);
            
            // Create admit card record
            AdmitCard::create([
                'student_id' => $student->id,
                'file_path' => $fileName,
                'generated_by' => $this->userId ?? 'system',
                'generated_at' => now(),
                'download_count' => 0,
            ]);
            
            Log::info("Admit card generated successfully for student: {$student->student_id}");
            
        } catch (\Exception $e) {
            Log::error("Failed to generate admit card for student {$this->studentId}: " . $e->getMessage());
            throw $e;
        } finally {
            $lock->release();
        }
    }
    
    public function failed(\Throwable $exception)
    {
        Log::error("Admit card generation job failed for student {$this->studentId}: " . $exception->getMessage());
    }
}