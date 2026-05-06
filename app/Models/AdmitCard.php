<?php
// app/Models/AdmitCard.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmitCard extends Model
{
    protected $table = 'admit_cards';
    
    protected $fillable = [
        'student_id', 'file_path', 'generated_by', 'generated_at', 'download_count'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function getDownloadUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}