<?php

namespace App\Jobs\WebSockets\Reverb;

use App\Events\UploadProgressEvent;
use App\Models\User;
use App\Notifications\UploadCompleteNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function handle(): void
    {
        $progress = 0;
        for ($i = 0; $i < 11; $i++) {
            UploadProgressEvent::dispatch($this->user, $progress);
            sleep(1);
            $progress += 10;
        }

        $this->user->notify(new UploadCompleteNotification());
    }
}
