<?php

namespace Code16\Sharp\Tests\Unit\Utils;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Filters\SelectFilter;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Links\LinkToForm;
use Code16\Sharp\Utils\Links\LinkToShowPage;
use Code16\Sharp\Utils\Links\LinkToSingleForm;
use Code16\Sharp\Utils\Links\LinkToSingleShowPage;

class SharpLinkToTest extends SharpTestCase
{

    /** @test */
    function we_can_generate_a_link_to_an_entity_list()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-list/my-entity" title="">test</a>',
            LinkToEntityList::make("my-entity")
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_a_link_to_an_entity_form()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-list/my-entity/s-form/my-entity/23" title="">test</a>',
            LinkToForm::make("my-entity", 23)
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_a_link_to_an_entity_form_through_a_show_page()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-list/my-entity/s-show/my-entity/23/s-form/my-entity/23" title="">test</a>',
            LinkToForm::make("my-entity", 23)
                ->throughShowPage()
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_a_link_to_an_entity_show()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-list/my-entity/s-show/my-entity/23" title="">test</a>',
            LinkToShowPage::make("my-entity", 23)
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_a_link_to_an_entity_single_show()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-show/my-entity" title="">test</a>',
            LinkToSingleShowPage::make("my-entity")
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_a_link_to_an_entity_single_form()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-show/my-entity/s-form/my-entity" title="">test</a>',
            LinkToSingleForm::make("my-entity")
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_a_link_to_an_entity_list_with_a_search()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-list/my-entity?search=my-search" title="">test</a>',
            LinkToEntityList::make("my-entity")
                ->setSearch("my-search")
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_a_link_to_an_entity_list_with_a_filter()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-list/my-entity?filter_country=France&filter_city=Paris" title="">test</a>',
            LinkToEntityList::make("my-entity")
                ->addFilter("country", "France")
                ->addFilter("city", "Paris")
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_a_link_to_an_entity_list_with_a_filter_classname()
    {
        $filter = new class extends SelectFilter {
            public function buildFilterConfig(): void
            {
                $this->configureKey("my-key");
            }

            public function values(): array
            {
                return [
                    1 => "one",
                    2 => "two",
                ];
            }
        };
        
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-list/my-entity?filter_my-key=1" title="">test</a>',
            LinkToEntityList::make("my-entity")
                ->addFilter($filter::class, 1)
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_a_link_to_an_entity_list_with_a_sort()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-list/my-entity?sort=name&dir=desc" title="">test</a>',
            LinkToEntityList::make("my-entity")
                ->setSort("name", "desc")
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_a_link_with_a_tooltip()
    {
        $this->assertEquals(
            '<a href="http://localhost/sharp/s-list/my-entity" title="tooltip">test</a>',
            LinkToEntityList::make("my-entity")
                ->setTooltip("tooltip")
                ->renderAsText("test")
        );
    }

    /** @test */
    function we_can_generate_an_url()
    {
        $this->assertEquals(
            'http://localhost/sharp/s-list/my-entity',
            LinkToEntityList::make("my-entity")
                ->renderAsUrl()
        );
    }
}
