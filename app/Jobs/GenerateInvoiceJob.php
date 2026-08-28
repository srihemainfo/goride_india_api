<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Throwable;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $jobDetails;
    public array $emailDetails;
    public array $payDetails;

    public function __construct(array $jobDetails, array $emailDetails, array $payDetails)
    {
        $this->jobDetails   = $jobDetails;
        $this->emailDetails = $emailDetails;
        $this->payDetails   = $payDetails;
    }

    public function handle(): void
    {
        Log::info('GenerateInvoiceJob started', [
            'job_no' => $this->jobDetails['job_no'] ?? null,
            'email'  => $this->emailDetails['email'] ?? null,
        ]);

        try {
            // Generate invoice number
            $invoiceNo = '#GRI-' . strtoupper(Str::random(10));

            $subject = 'Your Goride Booking Invoice ' . $invoiceNo;

            // $message = $this->buildEmailHtml();
            $message = '';

            // Check if invoice already exists
            $invoiceExists = DB::table('booking_invoice')
                ->where('job_no', $this->jobDetails['job_no'])
                ->exists();

            if ($invoiceExists) {
                Log::warning('Invoice already exists, skipping job', [
                    'job_no' => $this->jobDetails['job_no'],
                ]);
                return;
            }

            DB::transaction(function () use ($subject, $message) {
                
                // app(Controller::class)->composeEmail(
                //     'Queue',
                //     $this->emailDetails['email'],
                //     $subject,
                //     $message,
                //     '',
                //     []
                // );

                // Invoice record
                DB::table('booking_invoice')->insert([
                    'user_id'     => $this->jobDetails['user_id'],
                    'job_no'      => $this->jobDetails['job_no'],
                    'wallet_amt'  => $this->payDetails['wallet'] ?? 0,
                    'credit_amt'  => $this->payDetails['credit'] ?? 0,
                    'paid_amt'    => $this->payDetails['upi'] ?? 0,
                    'balance_amt' => $this->payDetails['upi'] == $this->jobDetails['part_pay_fare'] ? $this->jobDetails['pay_to_driver'] : 0,
                    'paid_log'    => json_encode($this->jobDetails),
                    'created_at'  => now(),
                    // 'updated_at'  => now(),
                ]);
            });

            Log::info('GenerateInvoiceJob completed successfully', [
                'job_no' => $this->jobDetails['job_no'],
            ]);

        } catch (Throwable $e) {

            Log::error('GenerateInvoiceJob failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'payload' => [
                    'jobDetails'   => $this->jobDetails,
                    'emailDetails' => $this->emailDetails,
                    'payDetails'   => $this->payDetails,
                ],
            ]);

            // Re-throw so Laravel marks job as failed
            throw $e;
        }
    }

//     private function buildEmailHtml(): string
//     {
//         $dropoff = !empty($this->jobDetails['dropoff_date'])
//             ? "<p>Dropoff: {$this->jobDetails['dropoff_date']}</p>"
//             : '';

//         return <<<HTML
// <!doctype html>
// <html>
// <body style="margin:0;font-family:Arial,Helvetica,sans-serif;background:#f6f6f8;">
// <table width="100%" cellpadding="0" cellspacing="0">
// <tr>
// <td align="center">

// <table width="600" style="background:#ffffff;padding:20px;border-radius:8px;">
// <tr>
// <td>

// <h2 style="color:#135bec;">Booking Confirmed</h2>

// <p>Job No: <strong>{$this->jobDetails['job_no']}</strong></p>
// <p>{$this->jobDetails['from_place']} → {$this->jobDetails['to_place']}</p>
// <p>Pickup: {$this->jobDetails['pickup_date']}</p>
// {$dropoff}

// <hr>

// <p><strong>Total Amount:</strong> ₹{$this->jobDetails['total_fare']}</p>

// <p>Thank you for choosing Goride.</p>

// </td>
// </tr>
// </table>

// </td>
// </tr>
// </table>
// </body>
// </html>
// HTML;
//     }
    
    private function buildEmailHtml(): string
{
    $job = $this->jobDetails;

    // Safe values
    $jobType      = $job['job_type'] ?? '';
    $jobNo        = $job['job_no'] ?? '';
    $from         = $job['from_place'] ?? '';
    $to           = $job['to_place'] ?? '';
    $formatDate = function ($date) {
        return $date
            ? Carbon::parse($date)->format('M d, Y • h:i A')
            : '—';
    };

    $pickup  = $formatDate($job['pickup_date'] ?? null);
    $dropoffDate = $formatDate($job['dropoff_date'] ?? null);
    
    $distance     = $job['distance'] ?? 0;
    $vehicleNo    = $job['vehicle_no'] ?? '—';

    $baseFare     = $job['base_fare'] ?? 0;
    $tax          = $job['tax'] ?? 0;
    $totalFare    = $job['total_fare'] ?? 0;

    // Conditional dropoff block
    $dropoffHtml = $dropoffDate
        ? "<p style=\"margin:4px 0 0;font-weight:bold;\">{$dropoffDate}</p>"
        : "<p style=\"margin:4px 0 0;font-size:12px;color:#999;\">N/A</p>";

    return <<<HTML
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Goride Booking Confirmation</title>
</head>
<body style="margin:0;padding:0;background-color:#f6f6f8;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f6f6f8;padding:20px 0;">
  <tr>
    <td align="center">

      <table width="800" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;">

        <!-- HEADER -->
        <tr>
          <td style="padding:20px 30px;border-bottom:1px solid #e7ebf3;">
            <table width="100%">
              <tr>
                <td style="font-size:20px;font-weight:bold;color:#135bec;">
                  Goride Pvt. Ltd.
                </td>
                <td align="right">
                  <span style="background:#135bec;color:#ffffff;font-size:12px;font-weight:bold;padding:8px 14px;border-radius:6px;">
                    ✔ Booking Confirmed
                  </span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- TRIP SUMMARY -->
        <tr>
          <td style="padding:25px 30px;">
            <table width="100%" style="border:1px solid #cfd7e7;border-radius:10px;background:#f6f6f8;">
              <tr>
                <td style="padding:20px;">
                  <p style="margin:0 0 6px 0;font-size:12px;color:#135bec;font-weight:bold;text-transform:uppercase;">
                    {$jobType}
                  </p>
                  <p style="margin:0 0 4px 0;font-size:13px;color:#4c669a;">
                    Job No: <strong>{$jobNo}</strong>
                  </p>
                  <h2 style="margin:8px 0;font-size:22px;color:#0d121b;">
                    {$from} → {$to}
                  </h2>
                  <p style="margin:0;font-size:13px;color:#4c669a;">
                    Thank you for choosing Goride. Your trip is scheduled.
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- DETAILS -->
        <tr>
          <td style="padding:0 30px 20px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="25%" style="padding:10px;">
                  <div style="border:1px solid #cfd7e7;border-radius:8px;padding:12px;">
                    <p style="margin:0;font-size:12px;color:#4c669a;">Pickup Date</p>
                    <p style="margin:4px 0 0;font-weight:bold;">{$pickup}</p>
                  </div>
                </td>

                <td width="25%" style="padding:10px;">
                  <div style="border:1px solid #cfd7e7;border-radius:8px;padding:12px;">
                    <p style="margin:0;font-size:12px;color:#4c669a;">Dropoff Date</p>
                    {$dropoffHtml}
                  </div>
                </td>

                <td width="25%" style="padding:10px;">
                  <div style="border:1px solid #cfd7e7;border-radius:8px;padding:12px;">
                    <p style="margin:0;font-size:12px;color:#4c669a;">Distance</p>
                    <p style="margin:4px 0 0;font-weight:bold;">{$distance} km</p>
                  </div>
                </td>

                <td width="25%" style="padding:10px;">
                  <div style="border:1px solid #cfd7e7;border-radius:8px;padding:12px;">
                    <p style="margin:0;font-size:12px;color:#4c669a;">Vehicle No</p>
                    <p style="margin:4px 0 0;font-weight:bold;color:#135bec;">{$vehicleNo}</p>
                  </div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- FARE -->
        <tr>
          <td style="padding:20px 30px;">
            <h3 style="margin:0 0 10px;font-size:16px;border-bottom:1px solid #e7ebf3;padding-bottom:8px;">
              Fare Summary
            </h3>

            <table width="100%">
              <tr>
                <td style="font-size:13px;color:#4c669a;">Base Fare</td>
                <td align="right" style="font-size:13px;font-weight:bold;">₹ {$baseFare}</td>
              </tr>
              <tr>
                <td style="font-size:13px;color:#4c669a;">Tax (GST)</td>
                <td align="right" style="font-size:13px;font-weight:bold;">₹ {$tax}</td>
              </tr>
              <tr>
                <td colspan="2" style="border-top:1px solid #e7ebf3;padding-top:10px;">
                  <table width="100%">
                    <tr>
                      <td style="font-weight:bold;">Total Amount</td>
                      <td align="right" style="font-weight:bold;color:#135bec;font-size:18px;">
                        ₹ {$totalFare}
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td style="background:#f6f6f8;padding:30px;text-align:center;border-top:1px solid #e7ebf3;">
            <p style="margin:0;font-weight:bold;">Goride Pvt. Ltd.</p>
            <p style="margin:6px 0;font-size:12px;color:#4c669a;">
              123 Travel Tower, Anna Salai, Chennai
            </p>
            <p style="margin:6px 0;font-size:12px;color:#4c669a;">
              support@goride.com | +91 98765 43210
            </p>
            <p style="margin-top:20px;font-size:11px;color:#4c669a;">
              © 2024 Goride Pvt. Ltd.
            </p>
          </td>
        </tr>

      </table>

    </td>
  </tr>
</table>

</body>
</html>
HTML;
}

}