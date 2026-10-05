<?php

namespace Code16\Sharp\Utils\Icons;

use BladeUI\Icons\Exceptions\SvgNotFound;

class IconManager
{
    /**
     * @throws SvgNotFound
     */
    public function iconToArray(?string $icon): ?array
    {
        if (! $icon) {
            return null;
        }

        return [
            'name' => $icon,
            'svg' => svg($icon)->toHtml(),
        ];
    }
}
