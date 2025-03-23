<?php

use App\Brand\Controllers\BrandController;
use Illuminate\Support\Facades\Route;

Route::controller(BrandController::class)->group(function() {
    Route::post('/brands', 'create');
    Route::patch('/brands/{brand}', 'update');
    Route::delete('/brands/{brand}', 'delete');
    Route::get('/brands', 'getAll');
    Route::get('/brands/{brand}', 'get');
});
