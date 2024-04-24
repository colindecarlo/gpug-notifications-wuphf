<?php

use App\Http\Controllers\Polling\LongPollingController;
use App\Http\Controllers\Polling\PollingController;
use App\Http\Controllers\ProfileController;
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
        Route::get('/upload', function (\Illuminate\Http\Request $request) {
            $user = $request->user();

            \App\Jobs\ServerSentEvents\ProcessUploadJob::dispatch($user);

            return view('sse.upload');
        })->name('upload');

        Route::get('/progress', function (\Illuminate\Http\Request $request) {
            return response()->stream(function () use ($request) {
                $lastProgress = \Illuminate\Support\Facades\Cache::get('sse:last-progress:user:' . $request->user()->id, 0);

                while (true) {
//                    echo "event: ping\n";

                    $progress = \Illuminate\Support\Facades\Cache::get('sse:progress-updates:user:' . $request->user()->id, $lastProgress);

                    if ($progress !== $lastProgress) {
                        \Illuminate\Support\Facades\Cache::put('sse:last-progress:user:' . $request->user()->id, $progress);
                        echo "data: " . json_encode(['progress' => $progress]) . "\n\n";
                    }

                    ob_flush();
                    flush();

                    if (connection_aborted()) {
                        break;
                    }

                    usleep(250_000);
                }
            }, 200, [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'text/event-stream',
                'X-Accel-Buffering' => 'no'
            ]);

        })->name('progress');
    });

Route::get('info', function () {
    phpinfo();
})->name('info');


require __DIR__ . '/auth.php';
