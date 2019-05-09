<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrandAndModelColumnsToSpaceshipTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spaceship_types', function (Blueprint $table) {
            $table->longText("brands")->nullable();
        });

        Schema::table('spaceships', function (Blueprint $table) {
            $table->string("brand")->nullable();
            $table->string("model")->nullable();
        });
    }
}
