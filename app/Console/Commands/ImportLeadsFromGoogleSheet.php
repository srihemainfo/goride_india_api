<?php

namespace App\Console\Commands;

use Google_Client;
use Google_Service_Sheets;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

class ImportLeadsFromGoogleSheet extends Command
{

    protected $signature = 'leads:import-google-sheet';

    protected $description = 'Import leads from Google Sheet to DB';

    public function handle()
    {
        // $client = new Google_Client();
        // $client->setAuthConfig(storage_path('app/google/go-ride-leads-3471bb16f794.json'));
        // $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);

        // $service = new Google_Service_Sheets($client);
        // $spreadsheetId = env('GOOGLE_SHEET_ID');
        // $range = 'TAMILNADU-June!A2:U'; // A2:E for name, email, phone, utm_source, utm_campaign

        // $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        // $values = $response->getValues();
        
        // $response = Http::get('https://script.google.com/macros/s/AKfycbw6GZTJJrptJ1bAbaltD_AqauGqxnqTFO4HRV35glGwadQdfoZbk_2H51dYaJec-DE/exec'); // One Day Leads
        $response = Http::get('https://script.google.com/macros/s/AKfycbxrDKb8nDpoq6fmLsJ9dgr6zaGg9e9m4EOv-HV-6DpU3BaWB-hr0qNvTwhlpF1CQwcg/exec'); // Ten Minutes Leads
        // $response = Http::get('https://script.google.com/macros/s/AKfycbwY3H5AFtWDM7QmKo6SVlHSWLKgbRkDXQ_vSsSgY_dkmxyf8GERt6NfQ-o5imlEVHCk/exec'); // 3 Days Leads
        $values = $response->json();
        // dd($values);
        
        $cron_start = now();
        
        if (empty($values)) {
            $this->info('No data found.');
            
            $newLead = DB::table('cron_logs')->insertGetId([
                'cron_name'         => 'goride_ad_leads_import_cron',
                'table_name'         => 'goride_ad_leads',
                'cron_start'         => $cron_start,
                'cron_end'        => now(),
                'affected_rows'        => 0,
                'error_note'   => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return;
        }
        
        // $cutoff = Carbon::now()->subDay();
        $cutoff = Carbon::now()->subMinutes(10);
        
        $insertCount = 0;
        try{
            
            foreach ($values as $row) {
            $explode = explode('+', $row[16]);
            
            $c_ph = $explode[1] ?? $row[16];
            
            $is_exists = DB::table('goride_ad_leads')->where(['phone' => $c_ph, 'form_id' => $row[8]])->exists();
            if(!$is_exists){
                
                $createdAt = Carbon::parse($row[1]);

                if (true) {
                // if ($createdAt->lessThanOrEqualTo($cutoff)) {
                    
                    $newLead = DB::table('goride_ad_leads')->insertGetId([
                        'row_id'         => $row[0] ?? null,
                        'ad_id'         => $row[2] ?? null,
                        'ad_name'        => $row[3] ?? null,
                        'adset_id'        => $row[4] ?? null,
                        'adset_name'   => $row[5] ?? null,
                        'campaign_id' => $row[6] ?? null,
                        'campaign_name' => $row[7] ?? null,
                        'form_id' => $row[8] ?? null,
                        'form_name' => $row[9] ?? null,
                        'is_organic' => $row[10] ?? null,
                        'platform' => $row[11] ?? null,
                        'business_type' => $row[12] ?? null,
                        'ready_to_revolutionize_your_fleet' => $row[13] ?? null,
                        'full_name' => $row[14] ?? null,
                        'email' => $row[15] ?? null,
                        'phone' => $c_ph ?? null,
                        'city' => $row[17] ?? null,
                        'is_qualified' => $row[18] ?? null,
                        'is_quality' => $row[19] ?? null,
                        'is_converted' => $row[20] ?? null,
                        'created_at' => $row[1] ?? null,
                        'updated_at' => now(),
                    ]);
                    
                    if($newLead){
                        $insertCount ++;
                    }
                    
                }
                
                
            }
        }
            
        $cron_end = now();
        
        $newLead = DB::table('cron_logs')->insertGetId([
            'cron_name'         => 'goride_ad_leads_import_cron',
            'table_name'         => 'goride_ad_leads',
            'cron_start'         => $cron_start,
            'cron_end'        => $cron_end,
            'affected_rows'        => $insertCount,
            'error_note'   => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
            
        }catch(\Exception $e){
            $cron_end = now();
            $error_note = $e->getMessage();
            
            $newLead = DB::table('cron_logs')->insertGetId([
                'cron_name'         => 'goride_ad_leads_import_cron',
                'table_name'         => 'goride_ad_leads',
                'cron_start'         => $cron_start,
                'cron_end'        => $cron_end,
                'affected_rows'        => $insertCount,
                'error_note'   => $error_note,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if($insertCount){
            $this->info('Leads imported successfully.');
        }else{
            $this->info('Leads are Up to date.');
            
        }
    }
}
