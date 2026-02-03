<?php

use Illuminate\Support\Facades\Route;

// Catch-all route for Vue SPA
// This ensures all frontend routes are handled by Vue Router
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
