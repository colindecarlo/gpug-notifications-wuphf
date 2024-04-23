<?php

namespace App\Jobs\WebSockets\Ratchet;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ZMQ;
use ZMQContext;

class ProcessUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function handle(): void
    {
        $context = new ZMQContext();
        $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect("tcp://localhost:5555");

        $progress = 0;

        for ($i = 0; $i < 11; $i++) {
            sleep(1);
            $socket->send(json_encode([
                'channel' => 'progress-updates:user:' . $this->user->id,
                'progress' => $progress
            ]));
            $progress += 10;
        }

        $socket->disconnect("tcp://localhost:5555");
    }
}
