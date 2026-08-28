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
        Schema::create('promocode', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->double('minvalue', 4, 2);   
            $table->double('maxvalue', 4, 2);
            $table->date('fromdate');
            $table->date('todate');
            $table->enum('type', ['Flat', 'Percentage']);
            $table->string('value');
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
        Schema::dropIfExists('promocode');
    }
};
