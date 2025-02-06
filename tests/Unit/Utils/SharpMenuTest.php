<?php

use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Utils\Menu\SharpMenu;

it('allows to add an entity link with its key in the menu', function () {
    sharp()->config()->addEntity('my-entity', PersonEntity::class);

    $menu = new class() extends SharpMenu
    {
        public function build(): SharpMenu
        {
            return $this->addEntityLink('my-entity', 'test', 'fa-user');
        }
    };

    expect($menu->build()->getItems()[0])
        ->getLabel()->toEqual('test')
        ->getIcon()->toEqual('fa-user')
        ->isEntity()->toBeTrue()
        ->getEntityKey()->toEqual('my-entity')
        ->getUrl()->toEqual(route('code16.sharp.list', 'my-entity'));
});

it('allows to add an entity link with its entity class name in the menu', function () {
    sharp()->config()->addEntity('my-entity', PersonEntity::class);

    $menu = new class() extends SharpMenu
    {
        public function build(): SharpMenu
        {
            return $this->addEntityLink(PersonEntity::class, 'test', 'fa-user');
        }
    };

    expect($menu->build()->getItems()[0])
        ->getLabel()->toEqual('test')
        ->getIcon()->toEqual('fa-user')
        ->isEntity()->toBeTrue()
        ->getEntityKey()->toEqual('my-entity')
        ->getUrl()->toEqual(route('code16.sharp.list', 'my-entity'));
});

it('allows to add an external link in the menu', function () {
    $menu = new class() extends SharpMenu
    {
        public function build(): SharpMenu
        {
            return $this->addExternalLink('https://code16.fr', 'test', 'fa-link');
        }
    };

    expect($menu->build()->getItems()[0])
        ->getLabel()->toEqual('test')
        ->getIcon()->toEqual('fa-link')
        ->isExternalLink()->toBeTrue()
        ->getEntityKey()->toBeNull()
        ->getUrl()->toEqual('https://code16.fr');
});

it('allows to group links in sections', function () {
    sharp()->config()->addEntity('my-entity', PersonEntity::class);

    $menu = new class() extends SharpMenu
    {
        public function build(): SharpMenu
        {
            return $this->addSection(
                'my section',
                fn ($section) => $section->addEntityLink('my-entity', 'test', 'fa-user')
            );
        }
    };

    expect($menu->build()->getItems()[0])
        ->getLabel()->toEqual('my section')
        ->isSection()->toBeTrue()
        ->isCollapsible()->toBeTrue()
        ->getItems()->toHaveCount(1);
});

it('allows to make collapsible / non-collapsible sections', function () {
    $menu = new class() extends SharpMenu
    {
        public function build(): SharpMenu
        {
            return $this
                ->addSection(
                    'my first section',
                    fn ($section) => $section
                        ->addExternalLink('https://code16.fr', 'test')
                )
                ->addSection(
                    'my second section',
                    fn ($section) => $section
                        ->setCollapsible(false)
                        ->addExternalLink('https://code16.fr', 'test')
                );
        }
    };

    expect($menu->build()->getItems())->toHaveCount(2);
    expect($menu->build()->getItems()[0]->isCollapsible())->toBeTrue();
    expect($menu->build()->getItems()[1]->isCollapsible())->toBeFalse();
});
