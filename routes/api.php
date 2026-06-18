<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CheckingController;

Route::middleware('api.token')->prefix('v1')->group(function () {
    Route::post('/dttot/check', [CheckingController::class, 'checkDttot']);
    Route::post('/pep/check', [CheckingController::class, 'checkPep']);
});
