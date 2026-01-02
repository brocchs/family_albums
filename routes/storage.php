<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

Route::get('/storage/{path}', function (Request $request, $path) {
    $file = Storage::disk('public')->path($path);
    
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    
    return response()->file($file);
})->where('path', '.*')->name('storage.serve');