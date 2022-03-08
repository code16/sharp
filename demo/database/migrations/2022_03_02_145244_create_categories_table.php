<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('category_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')
                ->constrained('posts')
                ->cascadeOnDelete();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
