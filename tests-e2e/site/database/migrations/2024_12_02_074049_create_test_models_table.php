<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // TODO after editing this file, run:
        //  > php artisan migrate:fresh --seed
        //  > php artisan snapshot:create e2e-seed

        Schema::create('test_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('autocomplete_local')->nullable();
            $table->unsignedInteger('autocomplete_remote')->nullable();
            $table->unsignedInteger('autocomplete_remote2')->nullable();
            $table->json('autocomplete_list')->nullable();
            $table->boolean('check')->nullable();
            $table->date('date')->nullable();
            $table->dateTime('date_time')->nullable();
            $table->time('time')->nullable();
            $table->text('editor_html')->nullable();
            $table->json('editor_html_localized')->nullable();
            $table->text('editor_markdown')->nullable();
            $table->json('geolocation')->nullable();
            $table->json('list')->nullable();
            $table->integer('number')->nullable();
            $table->unsignedInteger('select_dropdown')->nullable();
            $table->json('select_dropdown_multiple')->nullable();
            $table->json('select_checkboxes')->nullable();
            $table->unsignedInteger('select_radio')->nullable();
//            $table->json('tags')->nullable();
            $table->text('textarea')->nullable();
            $table->json('textarea_localized')->nullable();
            $table->string('text')->nullable();
            $table->json('text_localized')->nullable();
            $table->unsignedInteger('upload_id')->nullable();
            $table->unsignedInteger('order')->default(100);
            $table->timestamps();
        });
    }
};
