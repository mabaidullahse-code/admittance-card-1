<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AdmitCard;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class AdmitCardController extends Controller
{
    public function download($student_id)
    {
        $student = Student::where('student_id', $student_id)->firstOrFail();
        $fileName = "admit_cards/Admit_Card_{$student->student_id}.pdf";
        $storagePath = "public/{$fileName}";
        $fullPath = storage_path("app/{$storagePath}");

        // 1. Check if the file already exists in storage
        if (file_exists($fullPath)) {
            // Increment student download count
            $student->increment('ap_isdownload');
            
            // Find or create the admit card record to track downloads
            $admitCard = AdmitCard::firstOrCreate(
                ['student_id' => $student->id, 'file_path' => $fileName],
                ['generated_by' => 'system', 'generated_at' => now()]
            );
            $admitCard->increment('download_count');

            return response()->download($fullPath, "Admit_Card_{$student->student_id}.pdf");
        }

        // 2. If not exists, generate, save and then return
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        Pdf::view('pdf.admit-card', ['student' => $student])
            ->format('a4')
            ->disk('public')
            ->save($fileName);

        // Create admit card record
        AdmitCard::create([
            'student_id' => $student->id,
            'file_path' => $fileName,
            'generated_by' => 'student_portal_on_the_fly',
            'generated_at' => now(),
            'download_count' => 1,
        ]);

        // Increment student download count
        $student->increment('ap_isdownload');

        return response()->download($fullPath, "Admit_Card_{$student->student_id}.pdf");
    }
}
