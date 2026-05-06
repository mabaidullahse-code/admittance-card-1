<?php
// database/migrations/2024_01_01_000002_create_complaints_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('category', [
                'student_name', 'father_name', 'date_of_birth', 'cnic', 
                'mobile', 'email', 'domicile', 'exam_center', 'exam_fee', 
                'picture', 'roll_number', 'other'
            ]);
            $table->text('problem_details');
            $table->enum('status', ['pending', 'in_progress', 'solved', 'rejected'])->default('pending');
            $table->text('admin_remarks')->nullable();
            $table->timestamp('solved_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('category');
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
};