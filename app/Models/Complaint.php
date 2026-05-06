<?php
// app/Models/Complaint.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table = 'complaints';
    
    protected $fillable = [
        'student_id', 'category', 'problem_details', 'status', 'admin_remarks', 'solved_at', 'attachment_path'
    ];

    protected $casts = [
        'solved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function getCategoryLabelAttribute()
    {
        $labels = [
            'student_name' => 'Student Name',
            'father_name' => 'Father Name',
            'date_of_birth' => 'Date of Birth',
            'cnic' => 'CNIC Number',
            'mobile' => 'Mobile Number',
            'email' => 'Email Address',
            'domicile' => 'Domicile',
            'exam_center' => 'Exam Center',
            'exam_fee' => 'Exam Fee',
            'picture' => 'Profile Picture',
            'roll_number' => 'Roll Number',
            'other' => 'Other Information',
        ];
        
        return $labels[$this->category] ?? ucfirst($this->category);
    }
}