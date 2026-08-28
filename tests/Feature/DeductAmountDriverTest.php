<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeductAmountDriverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Freeze time for consistent testing
        Carbon::setTestNow(Carbon::parse('2026-04-04 14:00:00'));
    }

    /** @test */
    public function it_deducts_wallet_successfully()
    {
        $userId = DB::table('user_register')->insertGetId([
            'name' => 'Driver',
            'mobile' => '9999999999',
            'email' => 'driver@test.com',
            'walletBalance' => 500,
            'deletes' => '0'
        ]);

        $jobId = DB::table('cus_job_temp')->insertGetId([
            'assigned_to' => $userId,
            'job_status' => 'accept',
            'pickup_date' => Carbon::now()->addMinutes(58),
            'deductAmt' => 100,
            'isDeduct' => 0
        ]);

        Artisan::call('DeductAmount:Driver');

        $this->assertDatabaseHas('walletBalance_history', [
            'reference_id' => $jobId,
            'transaction_type' => 'DEBIT'
        ]);

        $this->assertDatabaseHas('cus_job_temp', [
            'id' => $jobId,
            'isDeduct' => 1
        ]);

        $this->assertDatabaseHas('user_register', [
            'id' => $userId,
            'walletBalance' => 400
        ]);
    }

    /** @test */
    public function it_skips_if_already_deducted()
    {
        $userId = DB::table('user_register')->insertGetId([
            'walletBalance' => 500,
            'deletes' => '0'
        ]);

        $jobId = DB::table('cus_job_temp')->insertGetId([
            'assigned_to' => $userId,
            'job_status' => 'accept',
            'pickup_date' => Carbon::now()->addMinutes(58),
            'deductAmt' => 100,
            'isDeduct' => 1
        ]);

        Artisan::call('DeductAmount:Driver');

        $this->assertDatabaseMissing('walletBalance_history', [
            'reference_id' => $jobId
        ]);
    }

    /** @test */
    public function it_skips_if_insufficient_balance()
    {
        $userId = DB::table('user_register')->insertGetId([
            'walletBalance' => 50,
            'deletes' => '0'
        ]);

        $jobId = DB::table('cus_job_temp')->insertGetId([
            'assigned_to' => $userId,
            'job_status' => 'accept',
            'pickup_date' => Carbon::now()->addMinutes(58),
            'deductAmt' => 100,
            'isDeduct' => 0
        ]);

        Artisan::call('DeductAmount:Driver');

        $this->assertDatabaseMissing('walletBalance_history', [
            'reference_id' => $jobId
        ]);

        $this->assertDatabaseHas('cus_job_temp', [
            'id' => $jobId,
            'isDeduct' => 0
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_deduction()
    {
        $userId = DB::table('user_register')->insertGetId([
            'walletBalance' => 500,
            'deletes' => '0'
        ]);

        $jobId = DB::table('cus_job_temp')->insertGetId([
            'assigned_to' => $userId,
            'job_status' => 'accept',
            'pickup_date' => Carbon::now()->addMinutes(58),
            'deductAmt' => 100,
            'isDeduct' => 0
        ]);

        Artisan::call('DeductAmount:Driver');
        Artisan::call('DeductAmount:Driver');

        $count = DB::table('walletBalance_history')
            ->where('reference_id', $jobId)
            ->count();

        $this->assertEquals(1, $count);
    }

    /** @test */
    public function it_skips_jobs_outside_time_window()
    {
        $userId = DB::table('user_register')->insertGetId([
            'walletBalance' => 500,
            'deletes' => '0'
        ]);

        $jobId = DB::table('cus_job_temp')->insertGetId([
            'assigned_to' => $userId,
            'job_status' => 'accept',
            'pickup_date' => Carbon::now()->addMinutes(120), // outside window
            'deductAmt' => 100,
            'isDeduct' => 0
        ]);

        Artisan::call('DeductAmount:Driver');

        $this->assertDatabaseMissing('walletBalance_history', [
            'reference_id' => $jobId
        ]);
    }

    /** @test */
    public function it_skips_if_user_not_found()
    {
        $jobId = DB::table('cus_job_temp')->insertGetId([
            'assigned_to' => 9999, // non-existing user
            'job_status' => 'accept',
            'pickup_date' => Carbon::now()->addMinutes(58),
            'deductAmt' => 100,
            'isDeduct' => 0
        ]);

        Artisan::call('DeductAmount:Driver');

        $this->assertDatabaseMissing('walletBalance_history', [
            'reference_id' => $jobId
        ]);
    }
}