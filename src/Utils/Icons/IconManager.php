<?php

namespace Code16\Sharp\Utils\Icons;

use BladeUI\Icons\Exceptions\SvgNotFound;
use BladeUI\Icons\Svg;

class IconManager
{
    protected function resolveBladeIcon(string $icon): ?Svg
    {
        if($nameFromLegacy = $this->resolveLegacyFontAwesomeBladeIconName($icon)) {
            try {
                return svg($nameFromLegacy);
            } catch (SvgNotFound) {
                return null; // for legacy "fa-" class names we don't want to throw (if owenvoke/blade-fontawesome is not installed)
            }
        }
        
        return svg($icon);
    }
    
    protected function resolveLegacyFontAwesomeBladeIconName(string $icon): ?string
    {
        if(preg_match('/(?:^| )fa-([a-z1-9-]+)(?: |$)/', $icon, $name)) {
            preg_match('/(?:^| )(fas|fab|far)(?: |$)/', $icon, $prefix);
            
            if(!isset($prefix[1]) && str_ends_with($name[1], '-o')) {
                return 'far-' . substr($name[1], 0, -2);
            }
            
            return ($prefix[1] ?? 'fas') . '-' . $name[1];
        }
        
        return null;
    }
    
    public function iconToArray(?string $icon): ?array
    {
        if(!$icon) {
            return null;
        }
        
        $svg = $this->resolveBladeIcon($icon);
        
        return $svg
            ? [
                'name' => $svg->name(),
                'svg' => $svg->toHtml(),
            ]
            : null;
    }
}
