<?php

namespace Code16\Sharp\Tests\Feature;

use Code16\Sharp\Tests\Feature\Api\BaseApiTest;

class MenuViewComposerTest extends BaseApiTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->login();
    }

    /** @test */
    function we_can_define_an_external_url_in_the_menu()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "external",
                    "icon" => "fa-globe",
                    "url" => "https://google.com"
                ]
            ]
        );

        $menu = $this->get('/sharp/')->getOriginalContent()["sharpMenu"];

        $this->assertArraySubset([
            "label" => "external",
            "icon" => "fa-globe",
            "url" => "https://google.com",
            "type" => "url"
        ], (array)$menu->menuItems[0]);
    }

    /** @test */
    function we_can_define_an_direct_entity_link_in_the_menu()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "people",
                    "icon" => "fa-user",
                    "entity" => "person"
                ]
            ]
        );

        $menu = $this->get('/sharp/')->getOriginalContent()["sharpMenu"];

        $this->assertArraySubset([
            "key" => "person",
            "label" => "people",
            "icon" => "fa-user",
            "type" => "entity"
        ], (array)$menu->menuItems[0]);
    }

    /** @test */
    function we_can_define_an_category_in_the_menu()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "Data",
                    "entities" => [
                        [
                            "label" => "people",
                            "icon" => "fa-user",
                            "entity" => "person"
                        ]
                    ]
                ]
            ]
        );

        $menu = $this->get('/sharp/')->getOriginalContent()["sharpMenu"];

        $this->assertEquals("Data", $menu->menuItems[0]->label);
        $this->assertEquals("category", $menu->menuItems[0]->type);

        $this->assertArraySubset([
            "key" => "person",
            "label" => "people",
            "icon" => "fa-user",
            "type" => "entity"
        ], (array)$menu->menuItems[0]->entities[0]);
    }

    /** @test */
    function we_can_define_an_category_in_the_menu_with_the_legacy_format()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "Data",
                    "entities" => [
                        "person" => [
                            "label" => "people",
                            "icon" => "fa-user",
                        ]
                    ]
                ]
            ]
        );

        $menu = $this->get('/sharp/')->getOriginalContent()["sharpMenu"];

        $this->assertArraySubset([
            "key" => "person",
            "label" => "people",
            "icon" => "fa-user",
            "type" => "entity"
        ], (array)$menu->menuItems[0]->entities[0]);
    }
}