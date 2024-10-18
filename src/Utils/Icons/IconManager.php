<?php

namespace Code16\Sharp\Utils\Icons;

class IconManager
{
    protected function resolveBladeIcon(string $bladeIconName): string
    {
        return svg($bladeIconName)->toHtml();
    }
    
    protected function resolveBladeIconName(string $icon): ?string
    {
        if($fontAwesomeBladeIconName = $this->resolveLegacyFontAwesomeBladeIconName($icon)) {
            return $fontAwesomeBladeIconName;
        }
        
        return $icon;
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
        
        $name = $this->resolveBladeIconName($icon);
        
        return [
            'name' => $name,
            'svg' => $name ? $this->resolveBladeIcon($name) : null,
        ];
    }
}
