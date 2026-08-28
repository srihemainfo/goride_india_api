<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PartitionManage extends Command
{
    protected $signature = 'partition:manage';
    protected $description = 'Add next month partition if not exists';

    public function handle()
    {
        $db = config('database.connections.mysql.database');

        // Next month partition
        $nextMonth = now()->addMonth();
        $partitionName = 'p' . $nextMonth->format('Ym');

        // सीमा date (next month +1)
        $lessThanDate = $nextMonth->copy()->addMonth()->startOfMonth()->format('Y-m-d');

        // Check partition exists
        $exists = DB::select("
            SELECT PARTITION_NAME 
            FROM INFORMATION_SCHEMA.PARTITIONS 
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = 'drivers_location_logs'
            AND PARTITION_NAME = ?
        ", [$db, $partitionName]);

        if (empty($exists)) {

            DB::statement("
                ALTER TABLE drivers_location_logs
                ADD PARTITION (
                    PARTITION {$partitionName} VALUES LESS THAN ('{$lessThanDate}')
                )
            ");

            $this->info("Created: {$partitionName}");

        } else {
            $this->info("Already exists: {$partitionName}");
        }
    }
}