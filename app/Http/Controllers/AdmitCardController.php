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

        // Log the download or generation
        AdmitCard::create([
            'student_id' => $student->id,
            'file_path' => "admit_cards/{$student->student_id}.pdf",
            'generated_by' => 'student_portal',
            'generated_at' => now(),
        ]);

        // Increment download count
        $student->increment('ap_isdownload');

        return Pdf::view('pdf.admit-card', ['student' => $student])
            ->format('a4')
            ->name("Admit_Card_{$student->student_id}.pdf");
    }
}
