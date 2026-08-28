<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('driver_no')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_no',15)->unique();
            $table->string('password');
            $table->string('vehicle_type');
            $table->string('profile_image_path')->nullable();
            $table->enum('isActive', [0, 1])->default(1);

            $table->text('address')->nullable();
            $table->double('commision_value', 10, 3)->nullable();
            $table->double('driver_booking_percentage', 5,2)->nullable();
            $table->string('booking_email')->nullable();
            $table->string('national_insurance_no')->nullable();
            $table->string('vehicle_reg_no')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->string('vehicle_make')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->double('number_of_seats', 5,2)->nullable();

            $table->string('vehicle_insurance')->nullable();
            $table->date('vehicle_insurance_expiry')->nullable();

            $table->string('vehicle_license')->nullable();
            $table->date('vehicle_license_expiry')->nullable();

            $table->string('pco_license_no')->nullable();
            $table->date('pco_license_no_expiry')->nullable();

            $table->string('driver_license_no')->nullable();
            $table->date('driver_license_no_expiry')->nullable();

            $table->string('mot_no')->nullable();
            $table->date('mot_no_expiry')->nullable();

            $table->double('refresh_time', 5,2)->nullable();
            $table->double('before_reminder_time', 5,2)->nullable();
            $table->double('start_journey_gaptime', 5,2)->nullable();
            $table->string('customer_call')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers');
    }
};
