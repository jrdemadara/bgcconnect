<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ListenForAlerts extends Command
{
    protected $signature = 'alerts:listen';
    protected $description = 'Listen for alert messages';

    public function handle()
    {
        $channel = 'sms';

        $this->info("Listening for messages on channel: $channel");

        Redis::subscribe([$channel], function ($message) {
            $this->info("Received message: $message");
        });
    }
}
