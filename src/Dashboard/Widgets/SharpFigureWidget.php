<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpFigureWidget extends SharpWidget
{
    public static function make(string $key): self
    {
        return new static($key, 'figure');
    }

    public function toArray(): array
    {
        return parent::buildArray([]);
    }

    public static function formatEvolution(?string $evolution): ?array
    {
        if($evolution === null) {
            return null;
        }
        
        $evolution = str($evolution);
        
        if($evolution->startsWith('-')) {
            return [
                'increase' => false,
                'value' => $evolution->substr(1)
            ];
        }
        
        return [
            'increase' => true,
            'value' => $evolution->startsWith('+') 
                ? $evolution->substr(1)
                : $evolution
        ];
    }
}
