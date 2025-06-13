<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\KahootGameController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::post('/kahoot-games', [KahootGameController::class, 'store']);
    Route::get('/kahoot-games', [KahootGameController::class, 'index']);
});
