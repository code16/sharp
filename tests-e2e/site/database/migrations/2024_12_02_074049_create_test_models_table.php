<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('test_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('autocomplete_local');
            $table->unsignedInteger('autocomplete_remote');
            $table->json('autocomplete_list');
            $table->boolean('check');
            $table->date('date');
            $table->dateTime('date_time');
            $table->time('time');
            $table->text('editor_html');
            $table->json('editor_html_localized');
            $table->text('editor_markdown');
            $table->json('editor_markdown_localized');
            $table->json('geolocation');
            $table->json('list');
            $table->integer('number');
            $table->unsignedInteger('select_dropdown');
            $table->unsignedInteger('select_checkboxes');
            $table->unsignedInteger('select_radios');
            $table->json('tags');
            $table->text('textarea');
            $table->string('text');
            $table->json('text_localized');
            $table->unsignedInteger('upload_id');
            $table->timestamps();
        });
    }
};
