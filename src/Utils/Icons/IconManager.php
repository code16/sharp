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
        $classes = str($icon)->explode(' ');
        
        if($name = $classes->first(fn ($class) => str($class)->startsWith('fa-'))) {
            $prefix = $classes->first(fn ($class) => in_array($class, ['fas', 'far', 'fab'])) ?? 'fas';
            return sprintf('%s-%s', $prefix, str($name)->after('fa-'));
        }
        
        return $icon;
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
