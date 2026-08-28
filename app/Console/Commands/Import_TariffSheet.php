<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google_Client;
use Google_Service_Sheets;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use DB;

set_time_limit(0);
ini_set('max_execution_time', 0);

class Import_TariffSheet extends Command
{
    protected $signature = 'tariff:import-tariff-sheet';

    protected $description = 'Import traiff from Google Sheet to DB';

    public function handle()
    {
        $cron_start = now();
        $insertCount = 0;
        $updateCount = 0;
    
        try {
            $response = Http::retry(3, 5000)
                ->timeout(600)
                ->get('https://script.googleusercontent.com/a/macros/cwd.co.in/echo?user_content_key=AehSKLjhOEdn8Eadz_V_8vdofr8eL_qGlsNn1FjYKRZynN7V0FSOQ3lC1iEuxyg29NQXkPk2QkAxqKHqManuEKrmgAYFy2nCnkEYDzT5I2Aawd9lsrMr5v27cANE6PCiajiO4smrp4voRAem29Yaj_UAIn3ykbf9vvN4bLjNqK05HIklPu-px15ml6Icf6qSZcJOoLTCbIh20-GQvPCIGSdO53DoZxXuqxG8YxRmlB6zNRH18hN3EzNqWb3T_Fl55x07--m2-F1NT2tm_spJtyOD9Qfdrms5JP3V6zkofg6bIGqGSoIncBA&lib=MuzIU6gUUaEZf78CFTvW8bvSvwF35Rp9g');
            
            $values = $response->json();
    
            if (empty($values)) {
                $this->logCron('No data found', $cron_start, 0, 0);
                $this->info('No data found.');
                return Command::SUCCESS;
            }
    
            $rows = collect($values)
                ->flatMap(function ($sheet) {
                    return collect($sheet)
                        ->skip(1)
                        ->map(function ($row) {
                            $clean = [];
                            foreach ($row as $k => $v) {
                                $key = preg_replace('/\s+/', ' ', trim(str_replace("\n", ' ', $k)));
                                $clean[$key] = trim($v);
                            }
                            return $clean;
                        });
                })
                ->filter(function ($r) {
                    return !empty($r['FROM']) && !empty($r['TO']);
                })
                ->values();

    
            if ($rows->isEmpty()) {
                $this->logCron('No valid rows found', $cron_start, 0, 0);
                $this->warn('No valid rows found.');
                return Command::SUCCESS;
            }
    
            $existing = DB::table('dynamic_pages_local')
                ->select('id', 'name', 'to_place')
                ->get()
                ->keyBy(function($r) {
                    return $r->name . '|' . $r->to_place;
                });

    
            $vehicles = [
                'mini_4'           => ['actual' => 'Mini 4+1 (Indica/Indigo A/C)',             'expected' => 'Expected_5'],
                'one_four'         => ['actual' => '1 to 4 Seater (Swift Zest/Etios A/C )',   'expected' => 'Expected_7'],
                'five_six'         => ['actual' => '5 to 6 Seater (Tavera/Xylo/Ertigo)',      'expected' => 'Expected_9'],
                'five_seven'       => ['actual' => 'SUV Plus 7+1 ( 5 to 7) (Innova)',         'expected' => 'Expected_11'],
                'eight_onethree'   => ['actual' => 'Tempo traveller ( 8 to 13)',              'expected' => 'Expected_13'],
                'onefour_oneeight' => ['actual' => 'Tempo traveller ( 14 to 18)',             'expected' => 'Expected_15'],
                'onenine_twoone'   => ['actual' => 'Mini Bus ( 19 to 21)',                    'expected' => 'Expected_17'],
                'twotwo_twofive'   => ['actual' => 'Mini Bus ( 22 to 25)',                    'expected' => 'Expected_19'],
                'twosix_fivezero'  => ['actual' => 'Bus ( 26 to 50)',                         'expected' => 'Expected_21'],
            ];
    
            $toInsert = [];
            $toUpdate = [];
    
            foreach ($rows as $cleanRow) {
                $key = $cleanRow['FROM'] . '|' . $cleanRow['TO'];
    
                $vehicleData = [];
                foreach ($vehicles as $column => $keys) {
                    $actual   = $cleanRow[$keys['actual']] ?? null;
                    $expected = $cleanRow[$keys['expected']] ?? null;
                    $vehicleData[$column] = ($actual || $expected)
                        ? json_encode(['Actual' => $actual, 'Expected' => $expected])
                        : null;
                }
    
                $baseData = [
                    'name'       => $cleanRow['FROM'],
                    'to_place'   => $cleanRow['TO'],
                    'kms'        => $cleanRow['KMS'] ?? null,
                    'country_code' => 'IN',
                    'updated_at' => now(),
                ] + $vehicleData;
    
                if (isset($existing[$key])) {
                    $toUpdate[] = $baseData;
                } else {
                    $toInsert[] = array_merge($baseData, ['created_at' => now()]);
                }
            }
    
            DB::transaction(function () use (&$insertCount, &$updateCount, $toInsert, $toUpdate) {
                foreach (array_chunk($toInsert, 300) as $chunk) {
                    DB::table('dynamic_pages_local')->insert($chunk);
                    $insertCount += count($chunk);
                }
    
                foreach (array_chunk($toUpdate, 300) as $chunk) {
                    foreach ($chunk as $row) {
                        DB::table('dynamic_pages_local')
                            ->where('name', $row['name'])
                            ->where('to_place', $row['to_place'])
                            ->update($row);
                    }
                    $updateCount += count($chunk);
                }
            });
    
            $this->logCron('Success', $cron_start, $insertCount, $updateCount);
    
            $this->info("Import complete. Inserted: {$insertCount}, Updated: {$updateCount}");
            return Command::SUCCESS;
    
        } catch (\Exception $e) {
            $this->logCron('Error: ' . $e->getMessage(), $cron_start, $insertCount, $updateCount);
            $this->error('CRON failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Helper: Log CRON results
     */
    private function logCron($note, $start, $insertCount, $updateCount)
    {
        DB::table('cron_logs')->insert([
            'cron_name'     => 'goride_tariff_import_cron',
            'table_name'    => 'goride_tariff',
            'cron_start'    => $start,
            'cron_end'      => now(),
            'affected_rows' => $insertCount + $updateCount,
            'error_note'    => $note,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

}
