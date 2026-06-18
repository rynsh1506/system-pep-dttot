<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CheckingController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('v1')->middleware([\App\Http\Middleware\CheckApiToken::class])->group(function () {
    Route::post('/dttot/check', [CheckingController::class, 'checkDttot']);
    Route::post('/pep/check', [CheckingController::class, 'checkPep']);
});
