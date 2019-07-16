<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpListWidget extends SharpWidget
{

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Dashboard\SharpWidgetValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([]);
    }

}