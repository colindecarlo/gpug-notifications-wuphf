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

Route::prefix('/websockets')
    ->name('ws.')
    ->group(function () {
        Route::prefix('/ratchet')
            ->name('ratchet')
            ->group(function () {

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

Route::middleware('auth')
    ->prefix('/polling')
    ->name('polling.')
    ->group(function () {
        Route::get('/upload', function (\Illuminate\Http\Request $request) {
            $user = $request->user();

            \App\Jobs\LongPolling\ProcessUploadJob::dispatch($user);

            return view('polling.upload');
        })->name('upload');

        Route::get('/progress', function (\Illuminate\Http\Request $request) {
            $progress = \Illuminate\Support\Facades\Cache::get('polling:progress-updates:user:' . $request->user()->id, 0);

            return response()->json([
                'progress' => $progress
            ]);
        })->name('progress');
    });

Route::middleware('auth')
    ->prefix('/long-polling')
    ->name('polling.')
    ->group(function () {
        Route::get('/upload', function (\Illuminate\Http\Request $request) {
            $user = $request->user();

            \App\Jobs\LongPolling\ProcessUploadJob::dispatch($user);

            return view('long-polling.upload');
        })->name('upload');

        Route::get('/progress', function (\Illuminate\Http\Request $request) {
            $lastProgress = \Illuminate\Support\Facades\Cache::get('long-polling:last-progress:user:' . $request->user()->id, 0);

            while (true) {
                $progress = \Illuminate\Support\Facades\Cache::get('long-polling:progress-updates:user:' . $request->user()->id, $lastProgress);

                \Illuminate\Support\Facades\Log::debug('Checking progress on upload for user ' . $request->user()->id . ': ' . $progress . '%' . ' (last progress: ' . $lastProgress . '%)');

                if ($progress !== $lastProgress) {
                    \Illuminate\Support\Facades\Cache::put('long-polling:last-progress:user:' . $request->user()->id, $progress);
                    return response()->json([
                        'progress' => $progress
                    ]);
                }

                usleep(2000);
            }
        })->name('progress');
    });


require __DIR__ . '/auth.php';
