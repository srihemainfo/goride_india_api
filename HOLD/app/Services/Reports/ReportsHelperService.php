<?php

namespace App\Services\Reports;

use Carbon\{Carbon, CarbonPeriod};

class ReportsHelperService
{
    public function getJobID($transaction_array)
    {
     
        $trans_count = $transaction_array != null ? count($transaction_array) : 0;
        $jobID_string = '';

        for ($i = 0; $i < $trans_count; $i++) {
            if ($i === 0) {
                $jobID_string .= $transaction_array[$i]->jobid;
            } else {
                $jobID_string .= "," . $transaction_array[$i]->jobid;
            }
        }

        return $jobID_string;
    }

    public function getTransID($transaction_array)
    {
        // return 
        $trans_count = $transaction_array != null ? count($transaction_array) : 0;
        $tranID_string = '';

        for ($i = 0; $i < $trans_count; $i++) {
            if ($i === 0) {
                $tranID_string .= $transaction_array[$i]->id;
            } else {
                $tranID_string .= "," . $transaction_array[$i]->id;
            }
        }

        return $tranID_string;
    }

    // public function getMonthArray()
    // {
    //     $month_array = [];
    //     $period_month = CarbonPeriod::create(get_first_pickup_date(), '1 month', Carbon::now()->subMonth());
    //     foreach ($period_month as $dt) {
    //         array_push($month_array, $dt->format("F-Y"));
    //     }
    //     return array_reverse($month_array);
    // }
    
        public function getMonthArray()
    {
        $month_array = [];
        
        $period_months = CarbonPeriod::create(now()->subMonths(2), '1 month', now());
        
        foreach ($period_months as $dt) {
            array_push($month_array, $dt->format("F-Y"));
        }
        
        $month_array = array_reverse($month_array);

         return $month_array;


    }
    

    // public function getWeekArray()
    // {
    //     $week_array = [];
    //     $period_week = CarbonPeriod::create(get_first_pickup_date(), '1 week', Carbon::now());
    //     foreach ($period_week as $dt) {
    //         array_push($week_array, $dt->startOfWeek()->format('d-m-Y') . ' to ' . $dt->endOfWeek()->format('d-m-Y'));
    //     }
    //     return array_reverse($week_array);
    // }
    
   
    public function getWeekArray()
    {
    $week_array = [];

    $period_week = CarbonPeriod::create(get_first_pickup_date(), '1 week', Carbon::now());
    
    foreach ($period_week as $dt) {
        array_push($week_array, $dt->startOfWeek()->format('d-m-Y') . ' to ' . $dt->endOfWeek()->format('d-m-Y'));
    }
    
    $week_array = array_reverse($week_array);
   
     return $week_array;

    } 
    
    
}
