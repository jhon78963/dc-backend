<?php

use App\Brand\Controllers\BrandController;
use Illuminate\Support\Facades\Route;

Route::controller(BrandController::class)->group(function() {
    Route::post('/measurement-units', 'create');
    Route::patch('/measurement-units/{measurement_unit}', 'update');
    Route::delete('/measurement-units/{measurement_unit}', 'delete');
    Route::get('/measurement-units', 'getAll');
    Route::get('/measurement-units/{measurement_unit}', 'get');
});
