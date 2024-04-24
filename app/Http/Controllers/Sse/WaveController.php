<?php

namespace App\Http\Controllers\Sse;

use App\Http\Controllers\Controller;
use App\Jobs\ServerSentEvents\Wave\ProcessUploadJob;
use Illuminate\Http\Request;

class WaveController extends Controller
{
    public function index()
    {
        return view('sse.wave.upload');
    }

    public function upload(Request $request)
    {
        $user = $request->user();

        ProcessUploadJob::dispatch($user);

        return response()->json([
            'message' => 'Upload started',
        ]);
    }
}
