<?php

namespace App\Sharp;

use Code16\Sharp\Utils\Menu\SharpMenu as BaseSharpMenu;
use Code16\Sharp\Utils\Menu\SharpMenuItemSection;

class SharpMenu extends BaseSharpMenu
{
    public function build(): self
    {
        return $this
            ->addSection('Company', function (SharpMenuItemSection $section) {
                $section
                    ->addEntityLink('company_dashboard', 'Dashboard', 'fas fa-tachometer-alt')
                    ->addEntityLink('spaceship', 'Spaceships', 'fas fa-space-shuttle')
                    ->addEntityLink('pilot', 'Pilots', 'fas fa-user');
            })
            ->addSection('Travels', function (SharpMenuItemSection $section) {
                $section
                    ->addEntityLink('travels_dashboard', 'Dashboard', 'fas fa-tachometer-alt')
                    ->addEntityLink('passenger', 'Passengers', 'fas fa-bed')
                    ->addEntityLink('travel', 'Travels', 'fas fa-suitcase')
                    ->addSeparator('Hidden separator 1')
                    ->addSeparator('Utilities')
                    ->addExternalLink('https://google.com', 'Some external link', 'fas fa-globe')
                    ->addSeparator('Hidden separator 2');
            })
            ->addSection('Admin', function (SharpMenuItemSection $section) {
                $section
                    ->addEntityLink('account', 'My account', 'fas fa-user')
                    ->addEntityLink('user', 'Sharp users', 'fas fa-user-secret');
            })
            ->addEntityLink('feature', 'Some powerful Features', 'fab fa-superpowers');
    }
}
