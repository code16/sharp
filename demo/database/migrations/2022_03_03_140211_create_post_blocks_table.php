<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('post_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // text, video, visuals
            $table->text('content')->nullable();
            $table->unsignedTinyInteger('order')->default(100);

            $table->foreignId('post_id')
                ->constrained('posts')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }
};
