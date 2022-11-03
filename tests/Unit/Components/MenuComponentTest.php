<?php

namespace Code16\Sharp\Tests\Unit\Components;

use Code16\Sharp\Tests\Fixtures\PersonalDashboardEntity;
use Code16\Sharp\Tests\Fixtures\PersonEntity;
use Code16\Sharp\Tests\Fixtures\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Menu\SharpMenu;
use Code16\Sharp\Utils\Menu\SharpMenuItemLink;
use Code16\Sharp\Utils\Menu\SharpMenuItemSection;
use Code16\Sharp\Utils\Menu\SharpMenuItemSeparator;
use Code16\Sharp\View\Components\Menu;
use Code16\Sharp\View\Components\Menu\MenuSection;

class MenuComponentTest extends SharpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(new User);
        $this->disableSharpAuthorizationChecks();
    }

    /** @test */
    public function we_can_define_an_external_url_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.menu', [
                [
                    'label' => 'external',
                    'icon' => 'fa-globe',
                    'url' => 'https://google.com',
                ],
            ],
        );

        $item = app(Menu::class)->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('external', $item->getLabel());
        $this->assertEquals('fa-globe', $item->getIcon());
        $this->assertEquals('https://google.com', $item->getUrl());
        $this->assertEquals(true, $item->isExternalLink());
    }

    /** @test */
    public function we_can_define_an_external_url_in_the_menu_via_class()
    {
        $this->app->bind('test_sharp_menu', function () {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this->addExternalLink('https://google.com', 'external', 'fa-globe');
                }
            };
        });

        $this->app['config']->set('sharp.menu', 'test_sharp_menu');

        $item = app(Menu::class)->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('external', $item->getLabel());
        $this->assertEquals('fa-globe', $item->getIcon());
        $this->assertEquals('https://google.com', $item->getUrl());
        $this->assertEquals(true, $item->isExternalLink());
    }

    /** @test */
    public function we_can_define_a_direct_entity_link_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class,
        );

        $this->app['config']->set(
            'sharp.menu', [
                [
                    'label' => 'people',
                    'icon' => 'fa-user',
                    'entity' => 'person',
                ],
            ],
        );

        $item = app(Menu::class)->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('person', $item->getKey());
        $this->assertEquals('people', $item->getLabel());
        $this->assertEquals('fa-user', $item->getIcon());
        $this->assertEquals(route('code16.sharp.list', 'person'), $item->getUrl());
        $this->assertEquals(true, $item->isEntity());
    }

    /** @test */
    public function we_can_define_a_direct_entity_link_in_the_menu_via_class()
    {
        $this->app->bind('test_sharp_menu', function () {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this->addEntityLink('person', 'people', 'fa-user');
                }
            };
        });
        $this->app['config']->set('sharp.menu', 'test_sharp_menu');
        $this->app['config']->set('sharp.entities.person', PersonEntity::class);

        $item = app(Menu::class)->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('person', $item->getKey());
        $this->assertEquals('people', $item->getLabel());
        $this->assertEquals('fa-user', $item->getIcon());
        $this->assertEquals(route('code16.sharp.list', 'person'), $item->getUrl());
        $this->assertEquals(true, $item->isEntity());
    }

    /** @test */
    public function we_can_define_a_category_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class,
        );

        $this->app['config']->set(
            'sharp.menu', [
                [
                    'label' => 'Data',
                    'entities' => [
                        [
                            'label' => 'people',
                            'icon' => 'fa-user',
                            'entity' => 'person',
                        ],
                    ],
                ],
            ],
        );

        $menu = app(Menu::class);

        $section = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemSection::class, $section);
        $this->assertEquals('Data', $section->getLabel());

        $item = $section->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('person', $item->getKey());
        $this->assertEquals('people', $item->getLabel());
        $this->assertEquals('fa-user', $item->getIcon());
        $this->assertEquals(route('code16.sharp.list', 'person'), $item->getUrl());
        $this->assertEquals(true, $item->isEntity());
    }

    /** @test */
    public function we_can_define_a_category_in_the_menu_via_class()
    {
        $this->app->bind('test_sharp_menu', function () {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this->addSection('Data', function ($section) {
                        $section->addEntityLink('person', 'people', 'fa-user');
                    });
                }
            };
        });
        $this->app['config']->set('sharp.menu', 'test_sharp_menu');
        $this->app['config']->set('sharp.entities.person', PersonEntity::class);

        $menu = app(Menu::class);

        $section = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemSection::class, $section);
        $this->assertEquals('Data', $section->getLabel());

        $item = $section->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('person', $item->getKey());
        $this->assertEquals('people', $item->getLabel());
        $this->assertEquals('fa-user', $item->getIcon());
        $this->assertEquals(route('code16.sharp.list', 'person'), $item->getUrl());
        $this->assertEquals(true, $item->isEntity());
    }

    /** @test */
    public function we_can_define_a_dashboard_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.entities.personal_dashboard',
            PersonalDashboardEntity::class,
        );

        $this->app['config']->set(
            'sharp.menu', [
                [
                    'label' => 'My Dashboard',
                    'icon' => 'fa-dashboard',
                    'dashboard' => 'personal_dashboard',
                ],
            ],
        );

        $menu = app(Menu::class);

        $item = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('personal_dashboard', $item->getKey());
        $this->assertEquals('My Dashboard', $item->getLabel());
        $this->assertEquals('fa-dashboard', $item->getIcon());
        $this->assertEquals(route('code16.sharp.dashboard', 'personal_dashboard'), $item->getUrl());
        $this->assertEquals(true, $item->isDashboardEntity());
    }

    /** @test */
    public function we_can_define_a_dashboard_in_the_menu_via_class()
    {
        $this->app->bind('test_sharp_menu', function () {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this->addEntityLink('personal_dashboard', 'My Dashboard', 'fa-dashboard');
                }
            };
        });
        $this->app['config']->set('sharp.menu', 'test_sharp_menu');

        $this->app['config']->set(
            'sharp.entities.personal_dashboard',
            PersonalDashboardEntity::class,
        );

        $menu = app(Menu::class);

        $item = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('personal_dashboard', $item->getKey());
        $this->assertEquals('My Dashboard', $item->getLabel());
        $this->assertEquals('fa-dashboard', $item->getIcon());
        $this->assertEquals(route('code16.sharp.dashboard', 'personal_dashboard'), $item->getUrl());
        $this->assertEquals(true, $item->isDashboardEntity());
    }

    /** @test */
    public function we_can_define_a_single_show_entity_link_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            SinglePersonEntity::class,
        );

        $this->app['config']->set(
            'sharp.menu', [
                [
                    'label' => 'people',
                    'icon' => 'fa-user',
                    'entity' => 'person',
                    'single' => true,
                ],
            ],
        );

        $menu = app(Menu::class);

        $item = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('person', $item->getKey());
        $this->assertEquals('people', $item->getLabel());
        $this->assertEquals('fa-user', $item->getIcon());
        $this->assertEquals(route('code16.sharp.single-show', 'person'), $item->getUrl());
        $this->assertEquals(true, $item->isEntity());
    }

    /** @test */
    public function we_can_define_a_single_show_entity_link_in_the_menu_via_class()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            SinglePersonEntity::class,
        );

        $this->app->bind('test_sharp_menu', function () {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this->addEntityLink('person', 'people', 'fa-user');
                }
            };
        });
        $this->app['config']->set('sharp.menu', 'test_sharp_menu');

        $menu = app(Menu::class);

        $item = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('person', $item->getKey());
        $this->assertEquals('people', $item->getLabel());
        $this->assertEquals('fa-user', $item->getIcon());
        $this->assertEquals(route('code16.sharp.single-show', 'person'), $item->getUrl());
        $this->assertEquals(true, $item->isEntity());
    }

    /** @test */
    public function we_can_define_a_separator_in_the_menu_via_config()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class,
        );

        $this->app['config']->set(
            'sharp.menu', [
                [
                    'label' => 'Data',
                    'entities' => [
                        [
                            'label' => 'people',
                            'icon' => 'fa-user',
                            'entity' => 'person',
                        ],
                        [
                            'separator' => true,
                            'label' => 'Separator',
                        ],
                        [
                            'label' => 'other people',
                            'icon' => 'fa-user-o',
                            'entity' => 'person',
                        ],
                    ],
                ],
            ],
        );

        $menu = app(Menu::class);

        $section = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemSection::class, $section);
        $this->assertEquals('people', $section->getItems()[0]->getLabel());
        $this->assertEquals('other people', $section->getItems()[2]->getLabel());

        $item = $section->getItems()[1];
        $this->assertInstanceOf(SharpMenuItemSeparator::class, $item);
        $this->assertEquals('Separator', $item->getLabel());
        $this->assertEquals(true, $item->isSeparator());
    }

    /** @test */
    public function we_can_define_a_separator_in_the_menu_via_class()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class,
        );

        $this->app->bind('test_sharp_menu', function () {
            return new class extends SharpMenu
            {
                public function build(): SharpMenu
                {
                    return $this
                        ->addSection('Data', function ($section) {
                            $section->addEntityLink('person', 'people', 'fa-user')
                                ->addSeparator('Separator')
                                ->addEntityLink('person', 'other people', 'fa-user-o');
                        });
                }
            };
        });
        $this->app['config']->set('sharp.menu', 'test_sharp_menu');

        $menu = app(Menu::class);

        $section = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemSection::class, $section);
        $this->assertEquals('people', $section->getItems()[0]->getLabel());
        $this->assertEquals('other people', $section->getItems()[2]->getLabel());

        $item = $section->getItems()[1];
        $this->assertInstanceOf(SharpMenuItemSeparator::class, $item);
        $this->assertEquals('Separator', $item->getLabel());
        $this->assertEquals(true, $item->isSeparator());
    }

    /** @test */
    public function separators_in_last_position_are_hidden()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class,
        );

        $this->app['config']->set(
            'sharp.menu', [
                [
                    'label' => 'Data',
                    'entities' => [
                        [
                            'label' => 'people',
                            'icon' => 'fa-user',
                            'entity' => 'person',
                        ],
                        [
                            'separator' => true,
                            'label' => 'Separator',
                        ],
                    ],
                ],
            ],
        );

        $menu = app(Menu::class);

        $this->assertCount(1, (new MenuSection($menu->getItems()[0]))->getItems());
    }

    /** @test */
    public function stacked_separators_are_hidden()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class,
        );

        $this->app['config']->set(
            'sharp.menu', [
                [
                    'label' => 'Data',
                    'entities' => [
                        [
                            'label' => 'people',
                            'icon' => 'fa-user',
                            'entity' => 'person',
                        ],
                        [
                            'separator' => true,
                            'label' => 'Not wanted',
                        ],
                        [
                            'separator' => true,
                            'label' => 'Separator',
                        ],
                        [
                            'label' => 'people',
                            'icon' => 'fa-user',
                            'entity' => 'person',
                        ],
                    ],
                ],
            ],
        );

        $menu = app(Menu::class);

        $this->assertCount(3, (new MenuSection($menu->getItems()[0]))->getItems());
    }
}
