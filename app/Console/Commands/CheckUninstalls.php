<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\AdminApiController;

class CheckUninstalls extends Command
{
    // The command you will type in the terminal
    protected $signature = 'app:check-uninstalls';

    protected $description = 'Manually check for uninstalled apps and log them';

    public function handle()
    {
        $this->info("Starting manual check for uninstalled apps...");

        $adminController = app(AdminApiController::class);
        $accessToken = $adminController->getAccessToken();

        if (!$accessToken) {
            $this->error('Could not get FCM Access Token. Ensure FIREBASE_PRO_ID and credentials are set.');
            return Command::FAILURE;
        }

        // Using FIREBASE_PRO_ID to match your .env file
        $url = "https://fcm.googleapis.com/v1/projects/" . env('FIREBASE_PRO_ID') . "/messages:send";

        // 1. Check Drivers (user_register)
        $this->info("Checking Drivers...");
        $drivers = DB::table('user_register')
            ->where('deletes', '0')
            ->whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->select('id', 'fcm_token')
            ->get();

        $this->processTokens($drivers, 'user_register', $url, $accessToken);

        // 2. Check Customers (customer_register)
        $this->info("Checking Customers...");
        $customers = DB::table('customer_register')
            ->where('deletes', '0')
            ->whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->select('id', 'fcm_token')
            ->get();

        $this->processTokens($customers, 'customer_register', $url, $accessToken);

        $this->info('Uninstall check completed successfully.');
        return Command::SUCCESS;
    }

    private function processTokens($users, $referenceTable, $url, $accessToken)
    {
        $uninstalledCount = 0;

        foreach ($users as $user) {
            // Clean the token to ensure no accidental spaces are causing the invalid argument
            $token = trim((string)$user->fcm_token);

            // Skip invalid or clearly broken tokens to prevent API errors
            if (empty($token) || strlen($token) < 10) {
                 $this->error("User ID {$user->id}: Skipped (Token is empty or suspiciously short)");
                 continue;
            }

            // Silent Ping Payload - No notification block so it doesn't alert the user
            $payload = [
                'message' => [
                    'token' => $token,
                    'data' => [
                        'action' => 'silent_ping'
                    ]
                ]
            ];

            $response = Http::withToken($accessToken)->post($url, $payload);

            if ($response->successful()) {
                $this->line("User ID {$user->id}: Token still marked active by Firebase.");
            } elseif ($response->failed()) {
                $errorData = $response->json();
                
                $errorCode = $errorData['error']['details'][0]['errorCode'] ?? ($errorData['error']['status'] ?? 'UNKNOWN_ERROR');
                // Grab the exact Google error message for debugging
                $errorMessage = $errorData['error']['message'] ?? 'No specific message provided.';

                if ($errorCode === 'UNREGISTERED') {
                    $this->info("User ID {$user->id}: UNINSTALLED (UNREGISTERED) detected!");
                    
                    // Prevent duplicate entries if you run the command multiple times
                    $exists = DB::table('uninstalled_users')
                        ->where('user_id', $user->id)
                        ->where('reference_table', $referenceTable)
                        ->exists();

                    if (!$exists) {
                        DB::table('uninstalled_users')->insert([
                            'user_id' => $user->id,
                            'reference_table' => $referenceTable
                        ]);
                    }

                    // Clear the dead token from their profile
                    DB::table($referenceTable)->where('id', $user->id)->update(['fcm_token' => null]);
                    
                    $uninstalledCount++;
                } else {
                    // Print the exact reason Firebase rejected the token
                    $this->error("User ID {$user->id}: Failed -> {$errorCode} | Details: {$errorMessage}");
                }
            }
        }

        $this->info("Found {$uninstalledCount} uninstalled apps in {$referenceTable}.");
    }
}