<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTechnicalReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('spaceship_id');
            $table->string("status");
            $table->text("comment")->nullable();
            $table->dateTime("starts_at");
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
        Schema::dropIfExists('technical_reviews');
    }
}
