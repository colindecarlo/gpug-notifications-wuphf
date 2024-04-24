<?php

namespace App\Http\Controllers\Polling;

use App\Http\Controllers\Controller;
use App\Jobs\Polling\ProcessUploadJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PollingController extends Controller
{
    public function index()
    {
        return view('polling.upload');
    }

    public function upload(Request $request)
    {
        $user = $request->user();

        ProcessUploadJob::dispatch($user);

        return response()->json([
            'message' => 'Upload process started'
        ]);
    }

    public function progress(Request $request)
    {
        $progress = Cache::get('polling:progress-updates:user:' . $request->user()->id, 0);

        return response()->json([
            'progress' => $progress
        ]);
    }
}
