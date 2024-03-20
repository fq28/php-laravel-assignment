<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/publishers', [\App\Http\Controllers\PublisherController::class, 'index'])->name('publishers.index');
Route::get('/publishers/create', [\App\Http\Controllers\PublisherController::class, 'create'])->name('publishers.create');
Route::post('/publishers', [\App\Http\Controllers\PublisherController::class, 'store'])->name('publishers.store');
Route::get('/publishers/{publisher}/edit', [\App\Http\Controllers\PublisherController::class, 'edit'])->name('publishers.edit');
Route::put('/publishers/{publisher}', [\App\Http\Controllers\PublisherController::class, 'update'])->name('publishers.update');

Route::get('/writers', [\App\Http\Controllers\WriterController::class, 'index'])->name('writers.index');
Route::get('/writers/create', [\App\Http\Controllers\WriterController::class, 'create'])->name('writers.create');
Route::post('/writers', [\App\Http\Controllers\WriterController::class, 'store'])->name('writers.store');
Route::get('/writers/{writer}/edit', [\App\Http\Controllers\WriterController::class, 'edit'])->name('writers.edit');
Route::put('/writers/{writer}', [\App\Http\Controllers\WriterController::class, 'update'])->name('writers.update');

Route::get('/books', [\App\Http\Controllers\BookController::class, 'index'])->name('books.index');
Route::get('/books/create', [\App\Http\Controllers\BookController::class, 'create'])->name('books.create');
Route::post('/books', [\App\Http\Controllers\BookController::class, 'store'])->name('books.store');
Route::get('/books/{book}/edit', [\App\Http\Controllers\BookController::class, 'edit'])->name('books.edit');
Route::put('/books/{book}', [\App\Http\Controllers\BookController::class, 'update'])->name('books.update');
Route::post('/books/{id}/score', [\App\Http\Controllers\BookController::class, 'score'])->name('books.score');