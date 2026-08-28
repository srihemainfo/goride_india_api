<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class BidPlacedToRedis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $bidPayload;
    protected $jobId;

    public function __construct(array $bidPayload, int $jobId)
    {
        $this->bidPayload = $bidPayload;
        $this->jobId = $jobId;
    }

    public function handle()
    {
        $key = "job:{$this->jobId}:events";
        
        Redis::rpush($key, json_encode([
            'type' => 'bid_placed',
            'data' => $this->bidPayload,
            'ts' => now()->toDateTimeString()
        ]));

        Redis::ltrim($key, -100, -1);
    }
}
