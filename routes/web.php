<?php

use App\Http\Controllers\ProfileController;
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

Route::prefix('/websockets')->name('ws.')->group(function () {
    Route::prefix('/ratchet')->name('ratchet')->group(function () {
        Route::middleware('auth')->get('/upload', function () {
            return view('ratchet.upload');
        })->name('upload');

        Route::get('/progress', function (\Illuminate\Http\Request $request) {
            $user = \App\Models\User::where('email', $request->input('email'))->first();

            abort_if(!$user, Illuminate\Http\Response::HTTP_BAD_REQUEST);

            \App\Jobs\WebSockets\Ratchet\ProcessUploadJob::dispatch($user);

            return "Progress updated";
        })->name('progress');
    });
});


require __DIR__.'/auth.php';
