<?php
// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $table = 'students';
    
    protected $fillable = [
        'student_id', 'student_name', 'father_name', 'gender', 'marital_status',
        'email', 'mobile', 'id_type', 'id_number', 'date_of_birth',
        'sorted_roll_number_uhs', 'roll_number', 'domicile', 'province', 'city', 'nationality',
        'father_profession', 'mother_profession', 'exam_name', 'exam_fee',
        'paid', 'paid_at', 'national_center', 'exam_preferred_date',
        'national_center_name', 'international_center_name',
        'national_other_center_name', 'international_other_center_name',
        'bank_account_title', 'bank_name', 'iban', 'cnic', 'contact_no',
        'profile_picture', 'centre', 'picture_path', 'ap_isdownload'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'exam_preferred_date' => 'date',
        'paid_at' => 'datetime',
        'paid' => 'boolean',
    ];

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function admitCards(): HasMany
    {
        return $this->hasMany(AdmitCard::class);
    }

    // Get picture path from local folder or DB for web
    public function getPicturePathAttribute()
    {
        // Check if DB column has a value
        if ($dbPath = $this->attributes['picture_path'] ?? null) {
            // If it starts with mdcatimages/, add storage/
            if (str_starts_with($dbPath, 'mdcatimages/')) {
                return '/storage/' . $dbPath;
            }
            // If it's a full storage path
            if (str_contains($dbPath, 'storage/app/public/')) {
                return asset(str_replace('storage/app/public/', 'storage/', $dbPath));
            }
            // Ensure path has leading slash if it starts with storage/
            if (str_starts_with($dbPath, 'storage/')) {
                return '/' . $dbPath;
            }
            return $dbPath;
        }

        if ($this->profile_picture) {
            return $this->profile_picture;
        }

        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG', 'WEBP'];
        
        foreach ($extensions as $ext) {
            $path = storage_path("app/public/mdcatimages/{$this->student_id}.{$ext}");
            if (file_exists($path)) {
                return asset("storage/mdcatimages/{$this->student_id}.{$ext}");
            }
        }
        
        return asset('images/default-avatar.png');
    }

    // Get absolute local path for PDF generation
    public function getPictureLocalPathAttribute()
    {
        // If DB has a path
        if ($dbPath = $this->attributes['picture_path'] ?? null) {
            // Normalize path by stripping potential web prefixes
            $cleanPath = ltrim($dbPath, '/');
            if (str_starts_with($cleanPath, 'storage/')) {
                $cleanPath = substr($cleanPath, 8); // remove 'storage/'
            }

            // Check if it exists in storage/app/public/
            $publicStoragePath = storage_path('app/public/' . $cleanPath);
            if (file_exists($publicStoragePath)) {
                return $publicStoragePath;
            }

            // If it's relative like mdcatimages/123.jpg
            if (file_exists(storage_path('app/public/' . $dbPath))) {
                return storage_path('app/public/' . $dbPath);
            }
            // If it's relative to project root
            if (file_exists(base_path($dbPath))) {
                return base_path($dbPath);
            }
            // If it's already absolute
            if (file_exists($dbPath)) {
                return $dbPath;
            }
        }

        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG', 'WEBP'];
        
        foreach ($extensions as $ext) {
            $path = storage_path("app/public/mdcatimages/{$this->student_id}.{$ext}");
            if (file_exists($path)) {
                return $path;
            }
        }
        
        return public_path('images/default-avatar.png');
    }

    // Clean name (remove extra spaces)
    public function getCleanStudentNameAttribute()
    {
        return preg_replace('/\s+/', ' ', trim($this->student_name));
    }

    // Get last 4 digits of CNIC
    public function getCnicLastFourAttribute()
    {
        $cleanCnic = preg_replace('/[^0-9]/', '', $this->cnic);
        return substr($cleanCnic, -4);
    }
    
    // Format CNIC with dashes
    public function getFormattedCnicAttribute()
    {
        $clean = preg_replace('/[^0-9]/', '', $this->cnic);
        if (strlen($clean) === 13) {
            return substr($clean, 0, 5) . '-' . substr($clean, 5, 7) . '-' . substr($clean, 12, 1);
        }
        return $this->cnic;
    }
}