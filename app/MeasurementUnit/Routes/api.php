<?php

use App\Brand\Controllers\BrandController;
use Illuminate\Support\Facades\Route;

Route::controller(BrandController::class)->group(function() {
    Route::post('/measurement_units', 'create');
    Route::patch('/measurement_units/{measurement_unit}', 'update');
    Route::delete('/measurement_units/{measurement_unit}', 'delete');
    Route::get('/measurement_units', 'getAll');
    Route::get('/measurement_units/{measurement_unit}', 'get');
});
