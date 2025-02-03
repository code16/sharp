<?php

namespace App\Sharp;

use Code16\Sharp\Utils\Menu\SharpMenu as BaseSharpMenu;

class SharpMenu extends BaseSharpMenu
{
    public function build(): self
    {
        return $this
            ->addEntityLink('test-models', 'Test Models')
            ->addEntityLink('test-models-single', 'Test Models single')
            ->addExternalLink('https://example.org', 'External link');
    }
}
