<?php

namespace App\Services\Settlements;

use Illuminate\Support\Facades\{DB};


class SettlementHelperService
{
    public function check_settlement_status($from_date, $to_date)
    {
        return DB::table('transaction')
            ->select('id')
            ->where('fromdate', $from_date)
            ->where('todate', $to_date)
            ->get()
            ->first()
            ->id;
    }

    public function is_booking_exist($from_date, $to_date)
    {
        return DB::table('bookinfo')
            ->where('order_status', '=', 'Completed')
            ->whereBetween('pickup_date', [$from_date, $to_date])
            ->count();
    }

    public function get_all_bookings_id_array($from_date, $to_date, $recalculate = false)
    {
        $query = DB::table('bookinfo')
            ->select('id')
            ->whereBetween('pickup_date', [$from_date, $to_date]);

        if ($recalculate) {
            $query->whereIn('order_status', ['Completed', 'settled']);
        } else {
            $query->where('order_status', '=', 'Completed');
        }

        $booking_id_array = $query->get()
            ->pluck('id')
            ->toArray();

        return $booking_id_array;
    }

    public function calculate_booking_columns($booking_ids)
    {
        return DB::update(DB::raw("UPDATE `bookinfo` SET
            deduct_profit = total - (driver_amount + car_park_amount),
            commision_profit = driver_amount * (commision / 100),
            driver_final = (driver_amount + car_park_amount) - commision_profit,
            order_status = 'settled',
            settlement = 'yes'
        WHERE id IN ($booking_ids)"));
    }

    public function create_transactions($from_date, $to_date, $booking_ids)
    {
        // dd(now()->toDateString());
        $booking_details = $this->get_all_bookings_details($booking_ids);
        $insert_transaction_rows = [];
        $insert_settle_history_rows = [];

        foreach ($booking_details as $booking) {
            $row = [];
            $driver_row = [];
            $row['driver_id'] = $booking->driver_id;
            $row['jobid'] = $booking->booking_ids;
            $row['comm'] = $booking->commision_profit;
            $row['car_park_amount'] = $booking->car_park_amount;
            $row['driver_amt'] = $booking->driver_amount;
            $row['total'] = $booking->total;
            $row['cash'] = $booking->total;
            $row['fromdate'] = $from_date;
            $row['todate'] = $to_date;
            $row['settle_date'] = now()->toDateString();
            $row['note'] = "Transaction ${from_date} to ${to_date}";
            $row['card'] = 0;
            $row['bank'] = 0;
            $row['credit'] = 0;
            $row['total_credit'] = 0;
            $row['old_balance'] = 0;

            $insert_transaction_rows[] = $row;
        }

        $transactions = DB::table('transaction')->insert($insert_transaction_rows);

        $recent_transactions = $this->get_recent_transactions($from_date, $to_date);

        foreach ($recent_transactions as $transaction) {
            $row = [];

            $row['old_balance'] = 0;
            $row['current_balance'] = 0;
            $row['note'] = "Transaction ${from_date} to ${to_date}";
            $row['mode'] = "settle";
            $row['driver'] = $transaction->driver_id;
            $row['trans_id'] = $transaction->id;
            $row['amount'] = $transaction->driver_amt;
            $row['date_time'] = now()->toDateTimeString();
            $row['month_wise'] = 0;

            $insert_settle_history_rows[] = $row;
        }


        $settle_histories = DB::table('settle_history')->insert($insert_settle_history_rows);

        $updated_drivers_count = DB::update(DB::raw(
            "UPDATE `driver`
            INNER JOIN `transaction`
            ON `driver`.`id` = `transaction`.`driver_id`
            SET
            `driver`.`actual` = transaction.total,
            `driver`.`commision` = transaction.comm,
            `driver`.`balance` = 0,
            `driver`.`total_balance` = 0
            WHERE `transaction`.`fromdate` = '$from_date' AND `transaction`.`todate` = '$to_date'"
        ));

        return $transactions && $settle_histories ? $updated_drivers_count  : 0;
    }

    public function get_all_bookings_details($booking_ids)
    {
        return DB::table('bookinfo')
            ->select(
                'driver_id',
                DB::raw('SUM(total) as total'),
                DB::raw('SUM(commision_profit) as commision_profit'),
                DB::raw('SUM(car_park_amount) as car_park_amount'),
                DB::raw('SUM(driver_amount) as driver_amount'),
                DB::raw('GROUP_CONCAT(id SEPARATOR ",") AS booking_ids')
            )
            ->whereIn('id', $booking_ids)
            ->groupBy('driver_id')
            ->get();
    }

    public function get_recent_transactions($from_date, $to_date)
    {
        return DB::table('transaction')
            ->select('*')
            ->where('fromdate', '=', $from_date)
            ->where('todate', '=', $to_date)
            ->get();
    }
}
