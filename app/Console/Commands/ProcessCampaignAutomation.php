<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\AutomationEventService;

class ProcessCampaignAutomation extends Command
{
    /*
    |--------------------------------------------------------------------------
    | Command Signature
    |--------------------------------------------------------------------------
    */

    protected $signature =
        'automation:campaign';

    /*
    |--------------------------------------------------------------------------
    | Command Description
    |--------------------------------------------------------------------------
    */

    protected $description =
        'Process recurring campaign automations';

    /*
    |--------------------------------------------------------------------------
    | Handle
    |--------------------------------------------------------------------------
    */
    
    

    public function handle()
    {
        try {

            \Log::info(
                'Campaign automation cron started'
            );

            /*
            |--------------------------------------------------------------------------
            | Process Campaigns
            |--------------------------------------------------------------------------
            */

            AutomationEventService::processCampaigns();

            \Log::info(
                'Campaign automation cron completed'
            );

            $this->info(
                'Campaign automation completed successfully'
            );

        } catch (\Exception $e) {

            \Log::error(
                'Campaign automation cron failed',
                [
                    'message' =>
                        $e->getMessage(),

                    'line' =>
                        $e->getLine(),

                    'file' =>
                        $e->getFile()
                ]
            );

            $this->error(
                'Campaign automation failed'
            );
        }
    }
}