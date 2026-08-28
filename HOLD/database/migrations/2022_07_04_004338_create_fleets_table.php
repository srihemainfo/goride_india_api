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
        Schema::create('fleets', function (Blueprint $table) {
            $table->id();
            $table->string('fleet_name', 30)->unique();
            $table->unsignedInteger('passengers', false, false);
            $table->unsignedInteger('min', false, false);
            $table->unsignedInteger('max', false, false);
            $table->unsignedInteger('luggage', false, false);
            $table->unsignedInteger('hand', false, false);
            $table->unsignedInteger('booster', false, false);
            $table->unsignedInteger('child_seat', false, false);
            $table->enum('isActive', [0, 1])->default(1);
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
        Schema::dropIfExists('fleets');
    }
};
