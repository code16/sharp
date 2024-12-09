<?php

namespace Code16\Sharp\Utils\Icons;

use BladeUI\Icons\Exceptions\SvgNotFound;

class IconManager
{
    protected function resolveLegacyFontAwesomeBladeIconName(string $icon): ?string
    {
        if (preg_match('/(?:^| )fa-([a-z1-9-]+)(?: |$)/', $icon, $name)) {
            preg_match('/(?:^| )(fas|fab|far)(?: |$)/', $icon, $prefix);

            if (! isset($prefix[1]) && str_ends_with($name[1], '-o')) {
                return 'far-'.substr($name[1], 0, -2);
            }

            return ($prefix[1] ?? 'fas').'-'.$name[1];
        }

        return null;
    }

    /**
     * @throws SvgNotFound
     */
    protected function legacyIconToArray(string $resolvedBladeIconName): ?array
    {
        try {
            return [
                'name' => $resolvedBladeIconName,
                'svg' => svg($resolvedBladeIconName)->toHtml(),
            ];
        } catch (SvgNotFound $e) {
            if (! str_contains($e->getMessage(), 'fontawesome')) {
                return null; // for legacy "fa-" class names we don't want to throw (if owenvoke/blade-fontawesome is not installed)
            }
            throw $e;
        }
    }

    /**
     * @throws SvgNotFound
     */
    public function iconToArray(?string $icon): ?array
    {
        if (! $icon) {
            return null;
        }

        if ($nameFromLegacy = $this->resolveLegacyFontAwesomeBladeIconName($icon)) {
            return $this->legacyIconToArray($nameFromLegacy);
        }

        return [
            'name' => $icon,
            'svg' => svg($icon)->toHtml(),
        ];
    }
}
