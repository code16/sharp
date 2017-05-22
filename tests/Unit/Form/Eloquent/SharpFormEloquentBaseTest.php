<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent;

use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;

abstract class SharpFormEloquentBaseTest extends SharpTestCase
{
    use DatabaseTransactions;

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger("mother_id")->nullable();
            $table->timestamps();
        });

        Schema::create('friends', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("person1_id");
            $table->unsignedInteger("person2_id");
            $table->timestamps();
        });
    }
}