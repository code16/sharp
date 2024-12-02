<?php

namespace App\Sharp;

use Code16\Sharp\Utils\Menu\SharpMenu as BaseSharpMenu;

class SharpMenu extends BaseSharpMenu
{
    public function build(): self
    {
        return $this
            ->addExternalLink('https://sharp.code16.fr/docs/guide/building-menu', 'Documentation', 'fa fa-book');
    }
}
