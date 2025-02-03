<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('test_tags', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('test_model_test_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_model_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
