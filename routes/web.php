<?php

use Illuminate\Support\Facades\Route;

// API routes are handled separately (in routes/api.php)

// Catch all frontend routes
Route::get('/{any}', function () {
    return file_get_contents(public_path('client/index.html'));
})->where('any', '^(?!api).*$');
