<?php

namespace App\Http\Controllers\WebSockets;

use App\Http\Controllers\Controller;
use App\Jobs\WebSockets\Reverb\ProcessUploadJob;
use Illuminate\Http\Request;

class ReverbController extends Controller
{
    public function index()
    {
        return view('ws.reverb.upload');
    }

    public function upload(Request $request)
    {
        ProcessUploadJob::dispatch($request->user());

        return response()->json([
            'message' => 'Upload process started',
        ]);
    }
}
