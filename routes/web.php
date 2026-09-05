<?php

use App\Http\Controllers\ContentItemController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SeriesController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/series/{series}', [SeriesController::class, 'show'])->name('series.show');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/courses/{slug}', [ContentItemController::class, 'course'])->name('courses.show');
Route::get('/books/{slug}', [ContentItemController::class, 'book'])->name('books.show');
Route::get('/announcements/{slug}', [ContentItemController::class, 'announcement'])->name('announcements.show');
Route::get('/donation', DonationController::class)->name('donation');
