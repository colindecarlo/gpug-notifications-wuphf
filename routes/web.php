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
        Route::middleware('auth')
            ->prefix('/ratchet')
            ->name('ratchet.')
            ->group(function () {

                Route::get('/upload', function (\Illuminate\Http\Request $request) {
                    \App\Jobs\WebSockets\Ratchet\ProcessUploadJob::dispatch($request->user());

                    return view('ratchet.upload');
                })->name('upload');

                Route::get('/progress', function (\Illuminate\Http\Request $request) {
                    $user = \App\Models\User::where('email', $request->input('email'))->first();

                    abort_if(!$user, Illuminate\Http\Response::HTTP_BAD_REQUEST);

                    \App\Jobs\WebSockets\Ratchet\ProcessUploadJob::dispatch($user);

                    return "Progress updated";
                })->name('progress');
            });

        Route::middleware('auth')
            ->prefix('/reverb')
            ->name('reverb.')
            ->group(function () {
                Route::get('/upload', function (\Illuminate\Http\Request $request) {
                    \App\Jobs\WebSockets\Reverb\ProcessUploadJob::dispatch($request->user());
                    return view('ws.reverb.upload');
                })->name('upload');
            });
    });

Route::middleware('auth')
    ->prefix('/polling')
    ->name('polling.')
    ->group(function () {
        Route::get('/upload', function (\Illuminate\Http\Request $request) {
            $user = $request->user();

            \App\Jobs\Polling\ProcessUploadJob::dispatch($user);

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
    ->name('long-polling.')
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

                usleep(200_000);
            }
        })->name('progress');
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
