<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdmitCardController;

Route::get('/', function () {
    return view('welcome_portal');
});

Route::get('/download-admit-card/{student_id}', [AdmitCardController::class, 'download'])->name('admit-card.download');
