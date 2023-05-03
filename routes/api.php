<?php

use App\Api\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('authenticate-provider', [AuthenticationController::class, 'authenticate'])->name('authenticate-provider');

Route::get('get-api-credentials', [AuthenticationController::class, 'getApiCredentials'])->name('get-api-credentials');
