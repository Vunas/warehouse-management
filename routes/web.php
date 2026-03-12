<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| 1. PUBLIC
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->group(function () {
        Route::resource('users', UserController::class);
    });
