<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SuggestionSongController;


// Auth Routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    
    Route::group(['middleware' => 'auth:api'], function() {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Routes
Route::group(['prefix' => 'v1'], function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Hello, World!']);
    });
    Route::post('songs', [SongController::class, 'store']);
    Route::get('songs', [SongController::class, 'getAllSongs']);
    Route::post('suggestion-songs', [SuggestionSongController::class, 'store']);
    Route::get('suggestion-songs', [SuggestionSongController::class, 'getAllSuggestionSongs']);
    Route::put('songs/{id}', [SongController::class, 'update']);
    Route::delete('songs/{id}', [SongController::class, 'delete']);
    Route::post('suggestion-songs/{id}/approve', [SuggestionSongController::class, 'approveSuggestionSong']);
    Route::post('suggestion-songs/{id}/reject', [SuggestionSongController::class, 'rejectSuggestionSong']);
});


