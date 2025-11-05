<?php

use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::delete('properties/multiple_delete', [PropertyController::class, 'multipleDelete']);
Route::apiResource('properties', PropertyController::class);

Route::post('/register', [UserController::class, 'register']);
