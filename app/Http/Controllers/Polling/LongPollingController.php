<?php

namespace App\Http\Controllers\Polling;

use App\Http\Controllers\Controller;
use App\Jobs\LongPolling\ProcessUploadJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LongPollingController extends Controller
{
    public function index()
    {
        return view('long-polling.upload');
    }

    public function upload(Request $request)
    {
        $user = $request->user();

        ProcessUploadJob::dispatch($user);

        return response()->json(['message' => 'Upload process started.']);
    }

    public function progress(Request $request)
    {
        $lastProgress = Cache::get('long-polling:last-progress:user:' . $request->user()->id, 0);

        while (true) {
            $progress = Cache::get('long-polling:progress-updates:user:' . $request->user()->id, $lastProgress);

            Log::debug('Checking progress on upload for user ' . $request->user()->id . ': ' . $progress . '%' . ' (last progress: ' . $lastProgress . '%)');

            if ($progress !== $lastProgress) {
                Cache::put('long-polling:last-progress:user:' . $request->user()->id, $progress);
                return response()->json([
                    'progress' => $progress
                ]);
            }

            usleep(200_000);
        }
    }

}
