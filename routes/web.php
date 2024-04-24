<?php

use App\Http\Controllers\Polling\LongPollingController;
use App\Http\Controllers\Polling\PollingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Sse\RawController;
use App\Http\Controllers\WebSockets\RatchetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('/websockets')
    ->name('ws.')
    ->group(function () {
        Route::middleware('auth')
            ->prefix('/ratchet')
            ->name('ratchet.')
            ->group(function () {
                Route::get('/', [RatchetController::class, 'index'])->name('index');
                Route::get('/upload', [RatchetController::class, 'upload'])->name('upload');
            });
    });

Route::middleware('auth')
    ->prefix('/polling')
    ->name('polling.')
    ->group(function () {
        Route::get('/', [PollingController::class, 'index'])->name('index');
        Route::get('/upload', [PollingController::class, 'upload'])->name('upload');
        Route::get('/progress', [PollingController::class, 'progress'])->name('progress');
    });

Route::middleware('auth')
    ->prefix('/long-polling')
    ->name('long-polling.')
    ->group(function () {
        Route::get('/', [LongPollingController::class, 'index'])->name('index');
        Route::get('/upload', [LongPollingController::class, 'upload'])->name('upload');
        Route::get('/progress', [LongPollingController::class, 'progress'])->name('progress');
    });

Route::middleware('auth')
    ->prefix('/server-sent-events')
    ->name('sse.')
    ->group(function () {
        Route::get('/', [RawController::class, 'index'])->name('index');
        Route::get('/upload', [RawController::class, 'upload'])->name('upload');
        Route::get('/progress', [RawController::class, 'progress'])->name('progress');
    });

require __DIR__ . '/auth.php';
