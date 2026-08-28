<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\FirebaseJobService;

class GenerateFrequencyJobs extends Command
{
    protected $signature = 'carpool:schedule-job';

    protected $description = 'Generate scheduled jobs from frequency jobs';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            
            $now = now();

            $targetDateTime = $now->copy()->addHours(12);

            $targetDay = $targetDateTime->format('l');

            $targetDate = $targetDateTime->format('Y-m-d');

            $targetHour = $targetDateTime->format('H');

            $frequencyJobs = DB::table('frequency_job as fj')
                            // Left join customer_register with conditional clause
                ->leftJoin('customer_register as cr', function ($join) {
                    $join->on('cr.id', '=', 'fj.user_id')
                         ->where('fj.global_type', '=', 'carpool');
                })
                // Left join user_register with a unique alias (ur)
                ->leftJoin('user_register as ur', function ($join) {
                    $join->on('ur.id', '=', 'fj.user_id')
                         ->where('fj.global_type', '=', 'dr_carpool');
                })
                ->whereRaw(
                    "FIND_IN_SET(?, REPLACE(fj.frequency_type, ' ', ''))",
                    [$targetDay]
                )
                ->where('fj.status', 0)
                ->select(
                    'fj.*',
                    // COALESCE selects the first non-null value between the two tables
                    DB::raw('COALESCE(cr.mobile, ur.mobile) as mobile'),
                    DB::raw('COALESCE(cr.name, ur.name) as name')
                )
                ->get();

            if ($frequencyJobs->isEmpty()) {
                $this->info('No frequency jobs found.');
                return Command::SUCCESS;
            }

            foreach ($frequencyJobs as $frequencyJob) {

                DB::beginTransaction();
                try {

                    $jobData = json_decode(
                        $frequencyJob->job_data,
                        true
                    );

                    if (empty($jobData)) {
                        DB::rollBack();
                        continue;
                    }

                    if (empty($jobData['pickup_date'])) {

                        DB::rollBack();

                        continue;
                    }

                    try {

                        $pickupTime = Carbon::parse(
                            $jobData['pickup_date']
                        )->format('H:i:s');

                    } catch (\Exception $e) {

                        DB::rollBack();

                        continue;
                    }

                    $pickupDateTime = Carbon::parse(
                        $targetDate . ' ' . $pickupTime
                    );

                    $windowStart = $now->copy()->addHours(12);

                    $windowEnd = $now->copy()->addHours(13);

                    if (
                        !$pickupDateTime->between(
                            $windowStart,
                            $windowEnd
                        )
                    ) {

                        DB::rollBack();

                        continue;
                    }

                    $jobData['pickup_date'] = $pickupDateTime
                        ->format('Y-m-d H:i:s');

                    $alreadyExists = DB::table('cus_job_temp')
                        ->where('pickup_date', $jobData['pickup_date'])
                        ->where('user_id', $frequencyJob->user_id)
                        ->where(
                            'from_place_id',
                            $frequencyJob->from_place_id
                        )
                        ->where(
                            'to_place_id',
                            $frequencyJob->to_place_id
                        )
                        ->exists();

                    if ($alreadyExists) {
                        DB::rollBack();
                        continue;
                    }
                    
                    $jobData['confirm_status'] = DB::table('cus_job_temp')
                                        ->where('job_no', $jobData['job_no'])
                                        ->value('confirm_status') ?? 1;

                    $jobNo = $this->generateJobNumber();

                    $hash = hash_hmac(
                        'sha256',
                        $jobNo .
                        'NEW_BOOKING' .
                        $frequencyJob->mobile,
                        config('app.key')
                    );

                    $jobData['job_no'] = $jobNo;

                    $jobData['user_id'] =
                        $frequencyJob->user_id;

                    $jobData['from_place_id'] =
                        $frequencyJob->from_place_id;

                    $jobData['to_place_id'] =
                        $frequencyJob->to_place_id;

                    $jobData['global_type'] =
                        $frequencyJob->global_type;

                    $jobData['short_hash'] = 
                        $this->generateShortPreview();

                    $jobData['preview_hash'] = $hash;
                    $jobData['preview_hash'] = $hash;
                    // $jobData['confirm_status'] = 1;
                    $jobData['created_at'] = now();
                    $jobData['updated_at'] = now();
                    
                    unset(
                        $jobData['id'],
                        $jobData['poster_name']
                    );

                    $tempJobId = DB::table('cus_job_temp')
                        ->insertGetId($jobData);

                    DB::table('frequency_job')
                        ->where('id', $frequencyJob->id)
                        ->update([
                            'last_generated_at' => now(),
                            'last_generated_temp_job_id' => $tempJobId,
                            'last_generated_job_no' => $jobNo,
                            'updated_at' => now(),
                        ]);

                    DB::table('frequency_job_logs')
                        ->insert([
                            'frequency_job_id' => $frequencyJob->id,
                            'temp_job_id' => $tempJobId,
                            'job_no' => $jobNo,
                            'status' => 1,
                            'remarks' => 'Job generated successfully',
                            'generated_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                    DB::commit();

                    $firebaseData = $jobData;
                    $firebaseData['id'] = $tempJobId;
                    $firebaseData['poster_name'] = $frequencyJob->name;

                    unset(
                        $firebaseData['confirm_status'],
                        $firebaseData['updated_at'],
                        $firebaseData['route_id']
                        
                    );

                    try {

                        $firebaseService = new FirebaseJobService('', '');
                        
                        
                        // $firebaseService->createFirebaseJob(
                        //     $jobNo,
                        //     $firebaseData
                        // );

                    } catch (\Exception $firebaseException) {

                        \Log::error('Firebase Push Failed', [

                            'frequency_job_id' =>
                                $frequencyJob->id,

                            'temp_job_id' =>
                                $tempJobId,

                            'job_no' =>
                                $jobNo,

                            'message' =>
                                $firebaseException->getMessage(),

                            'line' =>
                                $firebaseException->getLine(),
                        ]);
                    }

                } catch (\Exception $e) {

                    DB::rollBack();

                    DB::table('frequency_job_logs')

                        ->insert([

                            'frequency_job_id' =>
                                $frequencyJob->id,

                            'temp_job_id' => 0,

                            'job_no' => '',

                            'status' => 0,

                            'remarks' => $e->getMessage(),

                            'generated_at' => now(),

                            'created_at' => now(),

                            'updated_at' => now(),
                        ]);

                    \Log::error('Frequency Job Failed', [

                        'frequency_job_id' =>
                            $frequencyJob->id,

                        'message' =>
                            $e->getMessage(),

                        'line' =>
                            $e->getLine(),

                        'file' =>
                            $e->getFile(),
                    ]);
                }
            }

            $this->info('Frequency jobs generated successfully.');

            return Command::SUCCESS;

        } catch (\Exception $e) {

            \Log::error('Frequency Cron Failed', [

                'message' => $e->getMessage(),

                'line' => $e->getLine(),

                'file' => $e->getFile(),
            ]);

            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }

    private function generateJobNumber(): string
    {
        do {

            $jobNo = 'GRP-' .
                now()->format('ymd') .
                '-' .
                strtoupper(Str::random(7));

            $exists = DB::table('cus_job_temp')
                ->where('job_no', $jobNo)
                ->exists();

        } while ($exists);

        return $jobNo;
    }

    private function generateShortPreview(): string
    {
        do {

            $shortCode = env('SHORT_SLUG') .
                Str::random(8);

            $exists = DB::table('cus_job_temp')
                ->where('short_hash', $shortCode)
                ->exists();

        } while ($exists);

        return $shortCode;
    }
}