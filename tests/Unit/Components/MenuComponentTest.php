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
    public function we_can_define_an_external_url_in_the_menu()
    {
        $this->app['config']
            ->set(
                'sharp.menu', 
                new class extends SharpMenu {
                    public function build(): SharpMenu
                    {
                        return $this
                            ->addExternalLink('https://google.com', 'external', 'fa-globe');
                    }
                }
            );

        $item = app(Menu::class)->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('external', $item->getLabel());
        $this->assertEquals('fa-globe', $item->getIcon());
        $this->assertEquals('https://google.com', $item->getUrl());
        $this->assertTrue($item->isExternalLink());
    }

    /** @test */
    public function we_can_define_a_direct_entity_link_in_the_menu()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class,
        );

        $this->app['config']
            ->set(
                'sharp.menu',
                new class extends SharpMenu {
                    public function build(): SharpMenu
                    {
                        return $this
                            ->addEntityLink('person', 'people', 'fa-user');
                    }
                }
            );

        $item = app(Menu::class)->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('person', $item->getEntityKey());
        $this->assertEquals('people', $item->getLabel());
        $this->assertEquals('fa-user', $item->getIcon());
        $this->assertEquals(route('code16.sharp.list', 'person'), $item->getUrl());
        $this->assertEquals(true, $item->isEntity());
    }

    /** @test */
    public function we_can_define_a_category_in_the_menu()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class,
        );
        
        $this->app['config']
            ->set(
                'sharp.menu',
                new class extends SharpMenu {
                    public function build(): SharpMenu
                    {
                        return $this->addSection('Data', function ($section) {
                            $section->addEntityLink('person', 'people', 'fa-user');
                        });
                    }
                }
            );
        
        $menu = app(Menu::class);

        $section = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemSection::class, $section);
        $this->assertEquals('Data', $section->getLabel());

        $item = $section->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('person', $item->getEntityKey());
        $this->assertEquals('people', $item->getLabel());
        $this->assertEquals('fa-user', $item->getIcon());
        $this->assertEquals(route('code16.sharp.list', 'person'), $item->getUrl());
        $this->assertEquals(true, $item->isEntity());
    }

    /** @test */
    public function we_can_define_a_dashboard_in_the_menu()
    {
        $this->app['config']->set(
            'sharp.entities.personal_dashboard',
            PersonalDashboardEntity::class,
        );
        
        $this->app['config']
            ->set(
                'sharp.menu',
                new class extends SharpMenu {
                    public function build(): SharpMenu
                    {
                        return $this
                            ->addEntityLink('personal_dashboard', 'My Dashboard', 'fa-dashboard');
                    }
                }
            );

        $menu = app(Menu::class);

        $item = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('personal_dashboard', $item->getEntityKey());
        $this->assertEquals('My Dashboard', $item->getLabel());
        $this->assertEquals('fa-dashboard', $item->getIcon());
        $this->assertEquals(route('code16.sharp.dashboard', 'personal_dashboard'), $item->getUrl());
        $this->assertEquals(true, $item->isDashboardEntity());
    }

    /** @test */
    public function we_can_define_a_single_show_entity_link_in_the_menu()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            SinglePersonEntity::class,
        );

        $this->app['config']
            ->set(
                'sharp.menu',
                new class extends SharpMenu {
                    public function build(): SharpMenu
                    {
                        return $this->addEntityLink('person', 'people', 'fa-user');
                    }
                }
            );

        $menu = app(Menu::class);

        $item = $menu->getItems()[0];
        $this->assertInstanceOf(SharpMenuItemLink::class, $item);
        $this->assertEquals('person', $item->getEntityKey());
        $this->assertEquals('people', $item->getLabel());
        $this->assertEquals('fa-user', $item->getIcon());
        $this->assertEquals(route('code16.sharp.single-show', 'person'), $item->getUrl());
        $this->assertEquals(true, $item->isEntity());
    }
    
    /** @test */
    public function we_can_define_a_separator_in_the_menu_via_class()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class,
        );

        $this->app['config']
            ->set(
                'sharp.menu',
                new class extends SharpMenu {
                    public function build(): SharpMenu
                    {
                        return $this
                            ->addSection('Data', function ($section) {
                                $section->addEntityLink('person', 'people', 'fa-user')
                                    ->addSeparator('Separator')
                                    ->addEntityLink('person', 'other people', 'fa-user-o');
                            });
                    }
                }
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
    public function separators_in_last_position_are_hidden()
    {
        $this->app['config']->set(
            'sharp.entities.person',
            PersonEntity::class,
        );

        $this->app['config']
            ->set(
                'sharp.menu',
                new class extends SharpMenu {
                    public function build(): SharpMenu
                    {
                        return $this
                            ->addSection('Data', function ($section) {
                                $section->addEntityLink('person', 'people', 'fa-user')
                                    ->addSeparator('Separator');
                            });
                    }
                }
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

        $this->app['config']
            ->set(
                'sharp.menu',
                new class extends SharpMenu {
                    public function build(): SharpMenu
                    {
                        return $this
                            ->addSection('Data', function ($section) {
                                $section->addEntityLink('person', 'people', 'fa-user')
                                    ->addSeparator('Not wanted')
                                    ->addSeparator('Separator')
                                    ->addEntityLink('person', 'people', 'fa-user');
                            });
                    }
                }
            );

        $menu = app(Menu::class);

        $this->assertCount(3, (new MenuSection($menu->getItems()[0]))->getItems());
    }
}
