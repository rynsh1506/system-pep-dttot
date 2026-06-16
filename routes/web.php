<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, ['id', 'en', 'ja'])) {
        session()->put('locale', $lang);
    }
    return back();
})->name('locale.switch');

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('home');
    Route::get('/approvals', App\Livewire\Approvals::class)->name('approvals');
    Route::get('/add-data', App\Livewire\TerdugaForm::class)->name('add-data');
    Route::get('/upload-data', App\Livewire\UploadData::class)->name('upload-data');
    Route::get('/edit-data/{id}', App\Livewire\TerdugaForm::class)->name('edit-data');
    Route::get('/search', App\Livewire\SearchData::class)->name('search');
    Route::get('/detail/{id}', App\Livewire\TerdugaDetail::class)->name('detail');
    Route::get('/export-data', [App\Http\Controllers\ExportController::class, 'export'])->name('export-data');

    // Pengajuan Routes
    Route::get('/pengajuan', App\Livewire\Pengajuan\PengajuanIndex::class)->name('pengajuan');
    Route::get('/pengajuan/tambah', App\Livewire\Pengajuan\PengajuanForm::class)->name('pengajuan.tambah');
    Route::get('/pengajuan/proses/{id}', App\Livewire\Pengajuan\PengajuanProcess::class)->name('pengajuan.proses');

    // Reksaloan / HRD Routes
    Route::get('/reksaloan', App\Livewire\Reksaloan\ReksaloanIndex::class)->name('reksaloan');
    Route::get('/reksaloan/proses/{id}', App\Livewire\Reksaloan\ReksaloanProcess::class)->name('reksaloan.proses');

    // User Management
    Route::get('/users', App\Livewire\UserManagement::class)->name('users')->middleware('can:manage-users');
    
    Route::get('/logout', function () {
        Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
