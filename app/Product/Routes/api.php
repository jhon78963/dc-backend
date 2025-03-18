<?php

use App\Role\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::controller(RoleController::class)->group(function() {
    Route::post('/products', 'create');
    Route::patch('/products/{product}', 'update');
    Route::delete('/products/{product}', 'delete');
    Route::get('/products', 'getAll');
    Route::get('/products/{product}', 'get');
});
