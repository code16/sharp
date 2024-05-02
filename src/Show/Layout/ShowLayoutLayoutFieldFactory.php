<?php

namespace Code16\Sharp\Show\Layout;

use Closure;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Layout\LayoutFieldFactory;

class ShowLayoutLayoutFieldFactory implements LayoutFieldFactory
{
    
    public function __construct(
        protected FieldsContainer $fieldsContainer,
        protected bool $inEntityListSection = false,
    ) {
    }
    
    public function inEntityListSection(): self
    {
        return new static($this->fieldsContainer, true);
    }
    
    public function make(string $key, Closure $subLayoutCallback = null): ShowLayoutField
    {
        $field = collect($this->fieldsContainer->getFields())->firstWhere('key', $key);
        
        if(!$this->inEntityListSection && $field instanceof SharpShowEntityListField) {
            throw new \Exception("Field [$key] is an entity list field, and can't be used in a layout.");
        }
        
        return new ShowLayoutField($key, $subLayoutCallback);
    }
}
