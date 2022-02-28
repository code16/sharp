<?php

namespace Code16\Sharp\View\Utils;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

abstract class ContentComponent extends Component
{
    /**
     * Provide attributes as props to allow @props() call in component view
     * @return callable
     */
    public function resolveView(): callable
    {
        return function() {
            return $this->render()->with(
                collect($this->attributes->getAttributes())
                    ->mapWithKeys(function ($value, $key) {
                        return [Str::camel($key) => $value];
                    })
                    ->all()
            );
        };
    }
    
    abstract public function render(): View;
}
