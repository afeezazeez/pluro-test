<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FileController;


Route::prefix('v1')->group(function () {
    Route::post('upload',FileController::class)->name('file.upload');
});

