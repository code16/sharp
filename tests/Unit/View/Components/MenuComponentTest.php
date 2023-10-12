<?php

use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Utils\Menu\SharpMenu;
use Code16\Sharp\Utils\Menu\SharpMenuItemLink;
use Code16\Sharp\Utils\Menu\SharpMenuItemSection;
use Code16\Sharp\Utils\Menu\SharpMenuItemSeparator;
use Code16\Sharp\View\Components\Menu;
use Code16\Sharp\View\Components\Menu\MenuSection;

beforeEach(function () {
    login();
//    $this->disableSharpAuthorizationChecks();
});

it('allows to define an external url in the menu', function () {
    config()->set(
        'sharp.menu',
        new class extends SharpMenu
        {
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
});

it('allows to define a direct entity link in the menu', function () {
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );

    config()
        ->set(
            'sharp.menu',
            new class extends SharpMenu
            {
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
});

it('allows to define a category in the menu', function () {
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );

    config()
        ->set(
            'sharp.menu',
            new class extends SharpMenu
            {
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
});

it('allows to define a dashboard in the menu', function () {
    config()->set(
        'sharp.entities.personal_dashboard',
        DashboardEntity::class,
    );

    config()
        ->set(
            'sharp.menu',
            new class extends SharpMenu
            {
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
});

it('allows to define a single show entity link in the menu', function () {
    config()->set(
        'sharp.entities.person',
        SinglePersonEntity::class,
    );

    config()
        ->set(
            'sharp.menu',
            new class extends SharpMenu
            {
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
});

it('allows to define a separator in the menu via class', function () {
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );

    config()
        ->set(
            'sharp.menu',
            new class extends SharpMenu
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
});

it('separators in last position are hidden', function () {
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );

    config()
        ->set(
            'sharp.menu',
            new class extends SharpMenu
            {
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
});

it('stacked separators are hidden', function () {
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );

    config()
        ->set(
            'sharp.menu',
            new class extends SharpMenu
            {
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
});
