<?php

namespace App\Sharp\Entities;

use App\Sharp\TestModels\Single\TestModelSingleForm;
use App\Sharp\TestModels\Single\TestModelSingleShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class TestModelSingleEntity extends SharpEntity
{
    protected string $label = 'Test model single';
    protected bool $isSingle = true;
    protected ?string $show = TestModelSingleShow::class;
    protected ?string $form = TestModelSingleForm::class;
    protected array $prohibitedActions = [];
}
