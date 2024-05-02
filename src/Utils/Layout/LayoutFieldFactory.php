<?php

namespace Code16\Sharp\Utils\Layout;

interface LayoutFieldFactory
{
    public function make(string $key, \Closure $subLayoutCallback = null): LayoutField;
    
    public function inListItem(): self;
}
