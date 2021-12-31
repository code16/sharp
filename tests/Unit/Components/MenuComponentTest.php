<?php

namespace Code16\Sharp\Tests\Unit\Components;

use Code16\Sharp\Tests\Fixtures\PersonalDashboardEntity;
use Code16\Sharp\Tests\Fixtures\PersonEntity;
use Code16\Sharp\Tests\Fixtures\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Menu\SharpMenu;
use Code16\Sharp\View\Components\Menu;

class MenuComponentTest extends SharpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(new User);
        $this->disableSharpAuthorizationChecks();
    }

    /** @test */
    function we_can_define_an_external_url_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "external",
                    "icon" => "fa-globe",
                    "url" => "https://google.com"
                ]
            ]
        );
        
        $this->assertArraySubset(
            [
                "label" => "external",
                "icon" => "fa-globe",
                "url" => "https://google.com",
                "type" => "url"
            ], 
            (array)app(Menu::class)->getItems()[0]
        );
    }

    /** @test */
    function we_can_define_an_external_url_in_the_menu_via_class()
    {
        $this->app->bind("test_sharp_menu", function() {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this->addExternalLink("https://google.com", "external", "fa-globe");
                }
            };
        });
        
        $this->app['config']->set('sharp.menu', 'test_sharp_menu');

        $this->assertArraySubset(
            [
                "label" => "external",
                "icon" => "fa-globe",
                "url" => "https://google.com",
                "type" => "url"
            ],
            (array)app(Menu::class)->getItems()[0]
        );
    }

    /** @test */
    function we_can_define_a_direct_entity_link_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class
        );
        
        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "people",
                    "icon" => "fa-user",
                    "entity" => "person"
                ]
            ]
        );

        $this->assertArraySubset(
            [
                "key" => "person",
                "label" => "people",
                "icon" => "fa-user",
                "type" => "entity",
                "url" => route("code16.sharp.list", "person"),
            ], 
            (array)app(Menu::class)->getItems()[0]
        );
    }

    /** @test */
    function we_can_define_a_direct_entity_link_in_the_menu_via_class()
    {
        $this->app->bind("test_sharp_menu", function() {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this->addEntityLink("person", "people", "fa-user");
                }
            };
        });
        $this->app['config']->set('sharp.menu', 'test_sharp_menu');
        $this->app['config']->set('sharp.entities.person', PersonEntity::class);

        $this->assertArraySubset(
            [
                "key" => "person",
                "label" => "people",
                "icon" => "fa-user",
                "type" => "entity",
                "url" => route("code16.sharp.list", "person"),
            ],
            (array)app(Menu::class)->getItems()[0]
        );
    }

    /** @test */
    function we_can_define_a_category_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class
        );
        
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

        $menu = app(Menu::class);

        $this->assertEquals("Data", $menu->getItems()[0]->label);
        $this->assertEquals("category", $menu->getItems()[0]->type);

        $this->assertArraySubset(
            [
                "key" => "person",
                "label" => "people",
                "icon" => "fa-user",
                "type" => "entity",
                "url" => route("code16.sharp.list", "person"),
            ], 
            (array)$menu->getItems()[0]->entities[0]
        );
    }

    /** @test */
    function we_can_define_a_category_in_the_menu_via_class()
    {
        $this->app->bind("test_sharp_menu", function() {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this->addSection("Data", function($section) {
                        $section->addEntityLink("person", "people", "fa-user");
                    });
                }
            };
        });
        $this->app['config']->set('sharp.menu', 'test_sharp_menu');
        $this->app['config']->set('sharp.entities.person', PersonEntity::class);

        $menu = app(Menu::class);

        $this->assertEquals("Data", $menu->getItems()[0]->label);
        $this->assertEquals("category", $menu->getItems()[0]->type);

        $this->assertArraySubset(
            [
                "key" => "person",
                "label" => "people",
                "icon" => "fa-user",
                "type" => "entity",
                "url" => route("code16.sharp.list", "person"),
            ],
            (array)$menu->getItems()[0]->entities[0]
        );
    }

    /** @test */
    function we_can_define_a_dashboard_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.entities.personal_dashboard',
            PersonalDashboardEntity::class
        );
        
        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "My Dashboard",
                    "icon" => "fa-dashboard",
                    "dashboard" => "personal_dashboard"
                ]
            ]
        );

        $menu = app(Menu::class);

        $this->assertArraySubset(
            [
                "key" => "personal_dashboard",
                "label" => "My Dashboard",
                "icon" => "fa-dashboard",
                "type" => "dashboard",
                "url" => route("code16.sharp.dashboard", "personal_dashboard"),
            ], 
            (array)$menu->getItems()[0]
        );
    }

    /** @test */
    function we_can_define_a_dashboard_in_the_menu_via_class()
    {
        $this->app->bind("test_sharp_menu", function() {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this->addEntityLink("personal_dashboard", "My Dashboard", "fa-dashboard");
                }
            };
        });
        $this->app['config']->set('sharp.menu', 'test_sharp_menu');

        $this->app['config']->set(
            'sharp.entities.personal_dashboard',
            PersonalDashboardEntity::class
        );

        $this->assertArraySubset(
            [
                "key" => "personal_dashboard",
                "label" => "My Dashboard",
                "icon" => "fa-dashboard",
                "type" => "dashboard",
                "url" => route("code16.sharp.dashboard", "personal_dashboard"),
            ],
            (array)app(Menu::class)->getItems()[0]
        );
    }

    /** @test */
    function we_can_define_a_single_show_entity_link_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            SinglePersonEntity::class
        );
        
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

        $menu = app(Menu::class);

        $this->assertArraySubset(
            [
                "key" => "person",
                "label" => "people",
                "icon" => "fa-user",
                "type" => "entity",
                "url" => route("code16.sharp.single-show", "person"),
            ], 
            (array)$menu->getItems()[0]
        );
    }

    /** @test */
    function we_can_define_a_single_show_entity_link_in_the_menu_via_class()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            SinglePersonEntity::class
        );

        $this->app->bind("test_sharp_menu", function() {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this->addEntityLink("person", "people", "fa-user");
                }
            };
        });
        $this->app['config']->set('sharp.menu', 'test_sharp_menu');

        $this->assertArraySubset(
            [
                "key" => "person",
                "label" => "people",
                "icon" => "fa-user",
                "type" => "entity",
                "url" => route("code16.sharp.single-show", "person"),
            ],
            (array)app(Menu::class)->getItems()[0]
        );
    }

    /** @test */
    function we_can_define_a_separator_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class
        );
        
        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "Data",
                    "entities" => [
                        [
                            "label" => "people",
                            "icon" => "fa-user",
                            "entity" => "person"
                        ],
                        [
                            "separator" => true,
                            "label" => "Separator",
                        ],
                        [
                            "label" => "other people",
                            "icon" => "fa-user-o",
                            "entity" => "person"
                        ]
                    ]
                ]
            ]
        );

        $menu = app(Menu::class);

        $this->assertEquals("people", $menu->getItems()[0]->entities[0]->label);
        $this->assertEquals("other people", $menu->getItems()[0]->entities[2]->label);

        $this->assertEquals(
            [
                "type" => "separator",
                "key" => null,
                "label" => "Separator",
            ],
            (array)$menu->getItems()[0]->entities[1]
        );
    }

    /** @test */
    function separators_in_last_position_are_hidden()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class
        );
        
        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "Data",
                    "entities" => [
                        [
                            "label" => "people",
                            "icon" => "fa-user",
                            "entity" => "person"
                        ],
                        [
                            "separator" => true,
                            "label" => "Separator",
                        ],
                    ]
                ]
            ]
        );

        $menu = app(Menu::class);

        $this->assertCount(1, $menu->getItems()[0]->entities);
    }

    /** @test */
    function stacked_separators_are_hidden()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class
        );
        
        $this->app['config']->set(
            'sharp.menu', [
                [
                    "label" => "Data",
                    "entities" => [
                        [
                            "label" => "people",
                            "icon" => "fa-user",
                            "entity" => "person"
                        ],
                        [
                            "separator" => true,
                            "label" => "Not wanted",
                        ],
                        [
                            "separator" => true,
                            "label" => "Separator",
                        ],
                        [
                            "label" => "people",
                            "icon" => "fa-user",
                            "entity" => "person"
                        ],
                    ]
                ]
            ]
        );

        $menu = app(Menu::class);

        $this->assertCount(3, $menu->getItems()[0]->entities);
    }
}