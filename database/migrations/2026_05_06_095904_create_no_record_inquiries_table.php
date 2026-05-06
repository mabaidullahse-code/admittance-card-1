<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('no_record_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('cnic')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('status')->default('pending'); // pending, resolved
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('no_record_inquiries');
    }
};
