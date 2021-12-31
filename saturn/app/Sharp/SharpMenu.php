<?php

namespace App\Sharp;

use App\Sharp\Entities\CompanyDashboardEntity;
use Code16\Sharp\Utils\Menu\SharpMenu as BaseSharpMenu;
use Code16\Sharp\Utils\Menu\SharpMenuSection;

class SharpMenu extends BaseSharpMenu
{
    public function build(): void
    {
        $this
            ->addSection("Company", function(SharpMenuSection $section) {
                $section
                    ->addEntityLink(new CompanyDashboardEntity(), "fas fa-tachometer-alt", "Optional label")
                    ->addExternalLink("https://perdu.com", "fas fa-globe", "Some external link")
                    
                    ->addDashboardLink("Dashboard", "company_dashboard", "fas fa-tachometer-alt")
                    ->addEntityLink("Spaceships", "spaceship", "fas fa-space-shuttle")
                    ->addSingleEntityLink("My account", "account", "fas fa-user")
                    ->addSeparator("Utils")
                    ->addExternalLink("Some external link", "https://perdu.com", "fas fa-globe");
            })
            ->addDashboardLink("Dashboard", "company_dashboard", "fas fa-tachometer-alt")
            ->addEntityLink("Spaceships", "spaceship", "fas fa-space-shuttle")
            ->addSingleEntityLink("My account", "account", "fas fa-user")
            ->addExternalLink("Some external link", "https://perdu.com", "fas fa-globe");
    }
}