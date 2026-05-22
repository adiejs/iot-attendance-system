<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;

use Illuminate\Support\Facades\File;

Route::get('/lihat-foto/{filename}', function ($filename) {
    $path = storage_path('app/public/attendances/' . $filename);

    if (!File::exists($path)) {
        return response('File fisik tidak ditemukan', 404);
    }

    return response()->file($path);
})->where('filename', '.*');

// Endpoint untuk ESP32
Route::post('/attend', [AttendanceController::class, 'store']);
Route::post('/scan-uid', [AttendanceController::class, 'scanForRegistration']);
