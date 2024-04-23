<?php

namespace App\Jobs\Polling;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

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
            Cache::put('polling:progress-updates:user:' . $this->user->id, $progress);
            sleep(1);
            $progress += 10;
        }

        sleep(5);
        Cache::forget('polling:progress-updates:user:' . $this->user->id);
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->user->id))->dontRelease()->expireAfter(30)];
    }
}
