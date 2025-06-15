<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\KahootGameController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware(['auth:api', 'custom.throttle:api_kahoot'])->group(function () {
    Route::post('/kahoot-games', [KahootGameController::class, 'store']);
    Route::get('/kahoot-games', [KahootGameController::class, 'index']);
    Route::get('/kahoot-games/{id}', [KahootGameController::class, 'show']);
    Route::put('/kahoot-games/{id}', [KahootGameController::class, 'update']);
    Route::delete('/kahoot-games/{id}', [KahootGameController::class, 'destroy']);
});
