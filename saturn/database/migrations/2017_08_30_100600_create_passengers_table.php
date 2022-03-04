<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->date('birth_date');
            $table->string('gender');
            $table->unsignedInteger('travel_id');
            $table->string('travel_category');
            $table->timestamps();
        });

        Schema::create('travel_delegates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('travel_id');
            $table->unsignedInteger('passenger_id');
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
        Schema::dropIfExists('travel_delegates');
        Schema::dropIfExists('passengers');
    }
}
