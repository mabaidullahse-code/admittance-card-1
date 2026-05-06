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
            // Increment download count
            $student->increment('ap_isdownload');
            
            // Log the download activity if needed
            AdmitCard::create([
                'student_id' => $student->id,
                'file_path' => $fileName,
                'generated_by' => 'student_portal_cached',
                'generated_at' => now(),
            ]);

            return response()->download($fullPath, "Admit_Card_{$student->student_id}.pdf");
        }

        // 2. If not exists, generate, save and then return
        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        Pdf::view('pdf.admit-card', ['student' => $student])
            ->format('a4')
            ->disk('public')
            ->save($fileName);

        // Log the generation
        AdmitCard::create([
            'student_id' => $student->id,
            'file_path' => $fileName,
            'generated_by' => 'student_portal_on_the_fly',
            'generated_at' => now(),
        ]);

        // Increment download count
        $student->increment('ap_isdownload');

        return response()->download($fullPath, "Admit_Card_{$student->student_id}.pdf");
    }
}
