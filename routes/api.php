<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SuggestionSongController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\JwtAuthenticate;


// Auth Routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    
    Route::group(['middleware' => JwtAuthenticate::class], function() {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

//Rotas Abertas
Route::group(['prefix' => 'v1'], function () {
    Route::get('songs', [SongController::class, 'getAllSongs']);
});

// Routes para o usuário
Route::group(['prefix' => 'v1', 'middleware' => JwtAuthenticate::class], function () {
    Route::post('suggestion-songs', [SuggestionSongController::class, 'store']);
    // Routes para o admin
    Route::group(['middleware' => CheckAdmin::class, 'prefix' => 'admin'], function () {
        Route::post('suggestion-songs/approve', [SuggestionSongController::class, 'approveSuggestionSong']);
        Route::post('suggestion-songs/reject', [SuggestionSongController::class, 'rejectSuggestionSong']);
        Route::get('suggestion-songs', [SuggestionSongController::class, 'getAllSuggestionSongs']);
        Route::post('insert-song', [SongController::class, 'insertSong']);
        Route::put('songs', [SongController::class, 'update']);
        Route::delete('songs', [SongController::class, 'delete']);
    });
});

//Rota default caso não seja passado uma rota
Route::fallback(function () {
    return response()->json(['message' => 'Rota não encontrada'], 404);
});


