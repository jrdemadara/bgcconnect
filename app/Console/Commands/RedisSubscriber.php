<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscriber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:redis-subscriber';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to Redis channel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $channel = 'alert';

        Redis::subscribe([$channel], function ($message) {
            $this->info("Received message: $message");
        });
    }
}
