<?php

use App\Http\Controllers\Api\PropertyController;
use Illuminate\Support\Facades\Route;

Route::delete('properties/multiple_delete', [PropertyController::class, 'multipleDelete']);
Route::apiResource('properties', PropertyController::class);

