<?php

namespace Code16\Sharp\Utils\Transformers\Attributes;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

class SharpBadgeCustomTransformer implements SharpAttributeTransformer
{
    public function apply($value, $instance = null, $attribute = null)
    {
        if ($value === true) {
            return '<div style="width: .5rem; height: .5rem; background: var(--primary); border-radius: 50%"></div>';
        }

        return '';
    }
}
