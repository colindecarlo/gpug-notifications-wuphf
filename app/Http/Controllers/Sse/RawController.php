<?php

namespace App\Http\Controllers\Sse;

use App\Http\Controllers\Controller;
use App\Jobs\ServerSentEvents\ProcessUploadJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RawController extends Controller
{
    public function index()
    {
        return view('sse.upload');
    }

    public function upload(Request $request)
    {
        $user = $request->user();

        ProcessUploadJob::dispatch($user);

        return response()->json(['message' => 'Upload process started.']);
    }

    public function progress(Request $request)
    {
        return response()->stream(function () use ($request) {
            $lastProgress = Cache::get('sse:last-progress:user:' . $request->user()->id, 0);

            while (true) {
//                    echo "event: ping\n";

                $progress = Cache::get('sse:progress-updates:user:' . $request->user()->id, $lastProgress);

                if ($progress !== $lastProgress) {
                    Cache::put('sse:last-progress:user:' . $request->user()->id, $progress);
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
    }
}
