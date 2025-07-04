<?php

use App\Http\Controllers\KahootGameController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/kahoot-games', KahootGameController::class)->except(['index']);
    Route::get('/kahoot-games', [KahootGameController::class, 'index'])->name('kahoot-games.index');
    Route::post('/kahoot-games/filtered', [KahootGameController::class, 'filtered'])->name('kahoot-games.filtered');
});

require __DIR__.'/auth.php';
