<?php
// database/migrations/2024_01_01_000003_create_admit_cards_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admit_cards', function ($table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('file_path', 500);
            $table->string('generated_by', 255);
            $table->timestamp('generated_at');
            $table->integer('download_count')->default(0);
            $table->timestamps();
            
            $table->index('generated_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admit_cards');
    }
};