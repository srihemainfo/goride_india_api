<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateDistrictCoordinates extends Command
{
    protected $signature = 'districts:update-coordinates';

    protected $description = 'Update latitude and longitude using existing Google Place IDs';

    public function handle()
    {
        // $apiKey = env('GOOGLE_KEY_TWO');
        $apiKey = '';

        if (!$apiKey) {
            $this->error('GOOGLE_KEY not found.');
            return Command::FAILURE;
        }

        $districts = DB::table('districts')
            ->whereNotNull('place_id')
            ->where(function ($query) {
                $query->whereNull('latitude')
                      ->orWhereNull('longitude');
            })
            ->get();

        if ($districts->isEmpty()) {
            $this->info('All districts already have coordinates.');
            return Command::SUCCESS;
        }

        foreach ($districts as $district) {

            try {

                $response = Http::timeout(20)
                    ->retry(3, 1000)
                    ->get('https://maps.googleapis.com/maps/api/place/details/json', [
                        'place_id' => $district->place_id,
                        'fields'   => 'geometry,formatted_address',
                        'key'      => $apiKey,
                    ]);

                if (!$response->successful()) {
                    $this->error("HTTP Error: {$district->district_name}");
                    continue;
                }

                $result = $response->json();

                if (empty($result['result']['geometry']['location'])) {
                    $this->warn("No coordinates found: {$district->district_name}");
                    continue;
                }

                $location = $result['result']['geometry']['location'];

                DB::table('districts')
                    ->where('id', $district->id)
                    ->update([
                        'latitude'           => $location['lat'],
                        'longitude'          => $location['lng'],
                        'name'  => $result['result']['formatted_address'] ?? null,
                        'updated_at'         => now(),
                    ]);

                $this->info(
                    "✔ {$district->district_name} => {$location['lat']}, {$location['lng']}"
                );

            } catch (\Exception $e) {

                $this->error("Error: {$district->district_name}");
                $this->error($e->getMessage());

            }

            sleep(1);
        }

        $this->info('Coordinates updated successfully.');

        return Command::SUCCESS;
    }
}