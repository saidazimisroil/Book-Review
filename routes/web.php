<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::resource('books', BookController::class)
    ->only(['index','show']);

Route::resource('books.reviews', ReviewsController::class)
    ->only(['create', 'store']);

Route::post('books/{book}/reviews', [ReviewsController::class, 'store'])
    ->middleware('throttle:reviews')
    ->name('books.reviews.store');