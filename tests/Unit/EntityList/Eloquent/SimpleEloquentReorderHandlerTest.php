<?php

namespace Code16\Sharp\Tests\Unit\EntityList\Eloquent;

use Code16\Sharp\EntityList\Eloquent\SimpleEloquentReorderHandler;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\SharpEloquentBaseTest;

class SimpleEloquentReorderHandlerTest extends SharpEloquentBaseTest
{
    /** @test */
    function we_can_use_SimpleEloquentReorderHandler()
    {
        Person::create(['id' => 10, 'name' => 'Bob', 'order' => 1]);
        Person::create(['id' => 20, 'name' => 'Bob', 'order' => 2]);
        Person::create(['id' => 30, 'name' => 'Bob', 'order' => 3]);

        (new SimpleEloquentReorderHandler(Person::class))
            ->reorder([30, 10, 20]);
        
        $this->assertEquals([30, 10, 20], Person::orderBy('order')->pluck('id')->all());
    }

    /** @test */
    function we_can_use_SimpleEloquentReorderHandler_with_custom_order_attribute()
    {
        Person::create(['id' => 20, 'name' => 'Bob', 'order' => 3, 'age' => 22]);
        Person::create(['id' => 30, 'name' => 'Bob', 'order' => 2, 'age' => 32]);
        Person::create(['id' => 50, 'name' => 'Bob', 'order' => 1, 'age' => 90]);

        (new SimpleEloquentReorderHandler(Person::class))
            ->setOrderAttribute('age')
            ->reorder([50, 20, 30]);

        $this->assertEquals([50, 20, 30], Person::orderBy('age')->pluck('id')->all());
    }
}