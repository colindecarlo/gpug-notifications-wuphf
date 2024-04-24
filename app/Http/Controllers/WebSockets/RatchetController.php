<?php

namespace App\Http\Controllers\WebSockets;

use App\Http\Controllers\Controller;
use App\Jobs\WebSockets\Ratchet\ProcessUploadJob;
use Illuminate\Http\Request;

class RatchetController extends Controller
{
    public function index()
    {
        return view('ratchet.upload');
    }

    public function upload(Request $request)
    {
        ProcessUploadJob::dispatch($request->user());

        return response()->json([
            'message' => 'Upload started'
        ]);
    }
}
