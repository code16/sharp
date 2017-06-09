<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string("name");
            $table->timestamps();
        });

        Schema::create('pilot_spaceship', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("spaceship_id");
            $table->unsignedInteger("pilot_id");
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
