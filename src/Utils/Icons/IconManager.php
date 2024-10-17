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
        if(preg_match('/\bfa-([a-z1-9-]+)\b/', $icon, $name)) {
            preg_match('/\b(fas|fab|far)\b/', $icon, $prefix);
            
            if(!isset($prefix[1]) && str_ends_with($name[1], '-o')) {
                return 'far-' . substr($name[1], 0, -2);
            }
            
            return ($prefix[1] ?? 'fas') . '-' . $name[1];
        }
        
        return null;
    }
    
    public function iconToArray(?string $icon): ?array
    {
        if($icon === null) {
            return null;
        }
        
        $name = $this->resolveBladeIconName($icon);
        
        return [
            'name' => $name,
            'svg' => $name ? $this->resolveBladeIcon($name) : null,
        ];
    }
}
