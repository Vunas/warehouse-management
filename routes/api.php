<?php

use Illuminate\Support\Facades\Route;

Route::get('/api/inventory/{warehouse}', [App\Http\Controllers\OutboundOrderController::class, 'getInventoryApi']);
Route::get('/api/locations/{warehouse}', [App\Http\Controllers\InventoryController::class, 'getLocationsApi']);
