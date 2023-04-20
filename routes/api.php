<?php

use App\Api\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::prefix('api')->group(static function (): void {
    Route::post('authenticate-provider', [AuthenticationController::class, 'authenticate'])->name('authenticate-provider');
//});