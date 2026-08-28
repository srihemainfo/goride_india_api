<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateCrmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    
    public $tries = 3;
    public $backoff = 60;
    
    protected $crmID;
    protected $reqArr;
    protected $domainPrefix;
    protected $user_id;
    protected $host;
    
    public function __construct($crmID, array $reqArr, $domainPrefix, $user_id, $host)
    {
        $this->crmID = $crmID;
        $this->reqArr = $reqArr;
        $this->domainPrefix = $domainPrefix;
        $this->user_id = $user_id;
        $this->host = $host;
    }

    public function handle(): void
    {
        $crmID = $this->crmID;
        $user_id = $this->user_id;

        try {
            // Mark as processing
            DB::table('crm')
                ->where('id', $crmID)
                ->where('userID', $user_id)
                ->where('deletes', '0')
                ->update([
                    'crmStatus' => 'pending',
                    'updatedon' => now(),
                ]);

            // Call external API
            $response = Http::timeout(90)->post(env('SHICreateCRM'), $this->reqArr);
            $crmRes = $response->json();

            if ($response->successful() && isset($crmRes['status']) && $crmRes['status'] === 'success') {
                $crmArr = [
                    "crmRes" => json_encode($crmRes),
                    "partnerID" => $crmRes['data']['partner_id'] ?? null,
                    "updatedon" => now(),
                    "crmStatus" => 'generated',
                    "subDomainName" => $this->domainPrefix,
                    "fullDomain" => $this->domainPrefix . '.' . preg_replace('/^www\./', '', $this->host),
                ];

                DB::table('crm')
                    ->where('id', $crmID)
                    ->where('userID', $user_id)
                    ->where("deletes", '0')
                    ->update($crmArr);

                Log::info("GenerateCrmJob: CRM successfully created for user {$user_id}");
            } else {
                $this->markAsFailed($crmID, $user_id, $response->body());
                throw new \Exception('CRM creation failed (response not success).');
            }
        } catch (\Throwable $e) {
            $this->markAsFailed($crmID, $user_id, $e->getMessage());
            Log::error("GenerateCrmJob Exception: " . $e->getMessage());
            $this->fail($e);
        }
    }
    
    protected function markAsFailed($crmID, $user_id, $message)
    {
        DB::table('crm')
            ->where('id', $crmID)
            ->where('userID', $user_id)
            ->where("deletes", '0')
            ->update([
                'crmStatus' => 'pending',
                'updatedon' => now(),
                'crmRes' => json_encode(['error' => $message]),
            ]);
    }

    /**
     * Called when job fails permanently (after retries).
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateCrmJob failed permanently for CRM ID {$this->crmID}: " . $exception->getMessage());
    }
}
