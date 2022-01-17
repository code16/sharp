<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('features', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedSmallInteger('order')->default(100);
            $table->timestamps();
        });

        Schema::create('feature_spaceship', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('spaceship_id');
            $table->unsignedInteger('feature_id');
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
        Schema::dropIfExists('feature_spaceship');
        Schema::dropIfExists('features');
    }
}
