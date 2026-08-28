<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateDistrictPlaceIds extends Command
{
    protected $signature = 'districts:update-place-ids';

    protected $description = 'Fetch Google Place IDs for Tamil Nadu districts';

    public function handle()
    {
        // $apiKey = env('GOOGLE_KEY_TWO');
        $apiKey = '';

        if (!$apiKey) {
            $this->error('GOOGLE_KEY not found.');
            return Command::FAILURE;
        }

        $districts = DB::table('districts')
            ->whereNull('place_id')
            ->orWhere('place_id', '')
            ->get();

        if ($districts->isEmpty()) {
            $this->info('No districts found to update.');
            return Command::SUCCESS;
        }

        foreach ($districts as $district) {

            $query = $district->district_name . ', Tamil Nadu, India';

            try {

                $response = Http::timeout(20)
                    ->retry(3, 1000)
                    ->get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json', [
                        'input' => $query,
                        'inputtype' => 'textquery',
                        'fields' => 'place_id,name',
                        'key' => $apiKey,
                    ]);

                if (!$response->successful()) {
                    $this->error("HTTP Error : {$district->district_name}");
                    continue;
                }

                $result = $response->json();

                if (!empty($result['candidates'])) {

                    $placeId = $result['candidates'][0]['place_id'];

                    DB::table('districts')
                        ->where('id', $district->id)
                        ->update([
                            'name' => $query,
                            'place_id' => $placeId,
                            'updated_at' => now()
                        ]);

                    $this->info("✔ {$district->district_name} -> {$placeId}");

                } else {

                    $this->warn("✘ Place not found : {$district->district_name}");

                }

            } catch (\Exception $e) {

                $this->error("Error : {$district->district_name}");
                $this->error($e->getMessage());

            }

            sleep(1);
        }

        $this->info('Completed.');

        return Command::SUCCESS;
    }
}