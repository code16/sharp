<?php

namespace Code16\Sharp\Tests\Feature;

use Code16\Sharp\Tests\Feature\Api\BaseApiTest;

class MenuViewComposerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    /** @test */
    function we_can_define_an_external_url_in_the_menu()
    {
        $this->withoutExceptionHandling();
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

        $this->assertArrayContainsSubset([
            "label" => "external",
            "icon" => "fa-globe",
            "url" => "https://google.com",
            "type" => "url"
        ], (array)$menu->menuItems[0]);
    }

    /** @test */
    function we_can_define_a_direct_entity_link_in_the_menu()
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

        $menu = $this->followingRedirects()->get('/sharp/')->getOriginalContent()["sharpMenu"];

        $this->assertArrayContainsSubset([
            "key" => "person",
            "label" => "people",
            "icon" => "fa-user",
            "type" => "entity",
            "url" => route("code16.sharp.list", "person"),
        ], (array)$menu->menuItems[0]);
    }

    /** @test */
    function we_can_define_a_category_in_the_menu()
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

        $menu = $this->followingRedirects()->get('/sharp/')->getOriginalContent()["sharpMenu"];

        $this->assertEquals("Data", $menu->menuItems[0]->label);
        $this->assertEquals("category", $menu->menuItems[0]->type);

        $this->assertArrayContainsSubset([
            "key" => "person",
            "label" => "people",
            "icon" => "fa-user",
            "type" => "entity",
            "url" => route("code16.sharp.list", "person"),
        ], (array)$menu->menuItems[0]->entities[0]);
    }

    /** @test */
    function we_can_define_a_dashboard_in_the_menu()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "My Dashboard",
                    "icon" => "fa-dashboard",
                    "dashboard" => "personal_dashboard"
                ]
            ]
        );

        $menu = $this->followingRedirects()->get('/sharp/')->getOriginalContent()["sharpMenu"];

        $this->assertArrayContainsSubset([
            "key" => "personal_dashboard",
            "label" => "My Dashboard",
            "icon" => "fa-dashboard",
            "type" => "dashboard",
            "url" => route("code16.sharp.dashboard", "personal_dashboard"),
        ], (array)$menu->menuItems[0]);
    }

    /** @test */
    function we_can_define_a_single_show_entity_link_in_the_menu()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "people",
                    "icon" => "fa-user",
                    "entity" => "person",
                    "single" => true
                ]
            ]
        );

        $menu = $this->followingRedirects()->get('/sharp/')->getOriginalContent()["sharpMenu"];

        $this->assertArrayContainsSubset([
            "key" => "person",
            "label" => "people",
            "icon" => "fa-user",
            "type" => "entity",
            "url" => route("code16.sharp.show", "person"),
        ], (array)$menu->menuItems[0]);
    }
}