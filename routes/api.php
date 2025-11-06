<?php

use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])->name('passport.token');

Route::delete('properties/multiple_delete', [PropertyController::class, 'multipleDelete']);
Route::apiResource('properties', PropertyController::class);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
