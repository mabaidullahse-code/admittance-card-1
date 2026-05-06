<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            
            // All columns from CSV (based on header row)
            $table->string('student_id', 50)->nullable();
            $table->string('student_name', 255)->nullable();
            $table->string('father_name', 255)->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('id_type', 50)->nullable();
            $table->string('id_number', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('sorted_roll_number_uhs', 100)->nullable();
            $table->string('roll_number', 100)->nullable();
            $table->string('domicile', 255)->nullable();
            $table->string('province', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('father_profession', 255)->nullable();
            $table->string('mother_profession', 255)->nullable();
            $table->string('exam_name', 255)->nullable();
            $table->decimal('exam_fee', 10, 2)->nullable();
            $table->boolean('paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->string('national_center', 255)->nullable();
            $table->date('exam_preferred_date')->nullable();
            $table->string('national_center_name', 255)->nullable();
            $table->string('international_center_name', 255)->nullable();
            $table->string('national_other_center_name', 255)->nullable();
            $table->string('international_other_center_name', 255)->nullable();
            $table->string('bank_account_title', 255)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('iban', 50)->nullable();
            $table->string('cnic', 20)->nullable();
            $table->string('contact_no', 20)->nullable();
            $table->string('profile_picture', 500)->nullable();
            $table->string('centre', 500)->nullable();
            $table->string('picture_path', 500)->nullable();
            $table->string('ap_isdownload', 10)->default('0');
            
            $table->timestamps();
            
            // Add indexes
            $table->index('student_id');
            $table->index('student_name');
            $table->index('cnic');
            $table->index('exam_name');
            $table->index('city');
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};