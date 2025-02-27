<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class DynamicLabelPersonEntity extends PersonEntity
{

    protected string $entityKey = 'dynamic_person';
    protected function label(): ?string
    {
        return __('dynamic_label');
    }
}
