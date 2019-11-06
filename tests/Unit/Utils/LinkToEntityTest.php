<?php

namespace Code16\Sharp\Tests\Unit\Utils;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\LinkToEntity;

class LinkToEntityTest extends SharpTestCase
{

    /** @test */
    function we_can_generate_a_link_to_an_entity_list()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/list/my-entity" title="">test</a>',
            (new LinkToEntity("test", "my-entity"))->render()
        );
    }

    /** @test */
    function we_can_generate_a_link_to_an_entity_instance()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/form/my-entity/23" title="">test</a>',
            (new LinkToEntity("test", "my-entity"))->setInstanceId(23)->render()
        );
    }

    /** @test */
    function we_can_generate_a_link_with_a_search()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/list/my-entity?search=my-search" title="">test</a>',
            (new LinkToEntity("test", "my-entity"))->setSearch("my-search")->render()
        );
    }

    /** @test */
    function we_can_generate_a_link_with_a_filter()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/list/my-entity?filter_country=France&filter_city=Paris" title="">test</a>',
            (new LinkToEntity("test", "my-entity"))
                ->addFilter("country", "France")
                ->addFilter("city", "Paris")
                ->render()
        );
    }

    /** @test */
    function we_can_generate_a_link_with_a_sort()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/list/my-entity?sort=name&dir=desc" title="">test</a>',
            (new LinkToEntity("test", "my-entity"))->setSort("name", "desc")->render()
        );
    }

    /** @test */
    function we_can_generate_a_link_with_a_tooltip()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/list/my-entity" title="tooltip">test</a>',
            (new LinkToEntity("test", "my-entity"))->setTooltip("tooltip")->render()
        );
    }

    /** @test */
    function we_can_generate_an_url()
    {
        $this->assertEquals(
            'http://localhost/sharp/list/my-entity',
            (new LinkToEntity())->setEntityKey( "my-entity")->renderAsUrl()
        );
    }
}