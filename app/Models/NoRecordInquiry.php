<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoRecordInquiry extends Model
{
    protected $table = 'no_record_inquiries';

    protected $fillable = [
        'candidate_id',
        'student_name',
        'father_name',
        'cnic',
        'gender',
        'dob',
        'mobile',
        'email',
        'ip_address',
        'status',
        'admin_notes'
    ];
}
