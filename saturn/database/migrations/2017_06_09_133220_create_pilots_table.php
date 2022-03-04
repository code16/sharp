<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePilotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pilots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('role');
            $table->unsignedSmallInteger('xp')->nullable();
            $table->timestamps();
        });

        Schema::create('pilot_spaceship', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('spaceship_id');
            $table->unsignedInteger('pilot_id');
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
        Schema::dropIfExists('pilot_spaceship');
        Schema::dropIfExists('pilots');
    }
}
