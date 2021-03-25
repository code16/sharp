<?php

namespace Code16\Sharp\View\Components\Utils;

class Colors
{
    public static function luminosity(string $hex): float
    {
        [$red, $green, $blue] = str_split(ltrim($hex, '#'), 2);
    
        $r = hexdec($red) / 255;
        $g = hexdec($green) / 255;
        $b = hexdec($blue) / 255;
        
        if ($r <= 0.03928) {
            $r = $r / 12.92;
        } else {
            $r = pow((($r + 0.055) / 1.055), 2.4);
        }
    
        if ($g <= 0.03928) {
            $g = $g / 12.92;
        } else {
            $g = pow((($g + 0.055) / 1.055), 2.4);
        }
    
        if ($b <= 0.03928) {
            $b = $b / 12.92;
        } else {
            $b = pow((($b + 0.055) / 1.055), 2.4);
        }
    
        $luminosity = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
        return $luminosity;
    }
    
    public static function hexToHsl(string $hex): array
    {
        [$red, $green, $blue] = str_split(ltrim($hex, '#'), 2);
    
        [$hue, $saturation, $lightness] = self::rgbValueToHsl(
            hexdec($red),
            hexdec($green),
            hexdec($blue)
        );
        
        return [$hue, $saturation, $lightness];
    }
    
    public static function rgbValueToHsl($red, $green, $blue): array
    {
        $r = $red / 255;
        $g = $green / 255;
        $b = $blue / 255;
        
        $cmax = max($r, $g, $b);
        $cmin = min($r, $g, $b);
        $delta = $cmax - $cmin;
        
        $hue = 0;
        if ($delta != 0) {
            if ($r === $cmax) {
                $hue = 60 * fmod(($g - $b) / $delta, 6);
            }
            
            if ($g === $cmax) {
                $hue = 60 * ((($b - $r) / $delta) + 2);
            }
            
            if ($b === $cmax) {
                $hue = 60 * ((($r - $g) / $delta) + 4);
            }
        }
        
        $lightness = ($cmax + $cmin) / 2;
        
        $saturation = 0;
        
        if ($lightness > 0 && $lightness < 1) {
            $saturation = $delta / (1 - abs((2 * $lightness) - 1));
        }
        
        return [$hue, min($saturation, 1) * 100, min($lightness, 1) * 100];
    }
}
