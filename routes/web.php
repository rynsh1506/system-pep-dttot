<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');
});
