<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spaceship_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('spaceships', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('capacity');
            $table->date('construction_date')->nullable();
            $table->unsignedInteger('type_id');
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
        Schema::dropIfExists('spaceships');
        Schema::dropIfExists('spaceship_types');
    }
}
