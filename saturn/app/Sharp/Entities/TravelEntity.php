<?php

namespace App\Sharp\Entities;

use App\Sharp\TravelSharpForm;
use App\Sharp\TravelSharpList;
use Code16\Sharp\Utils\Entities\SharpEntity;

class TravelEntity extends SharpEntity
{
    protected ?string $list = TravelSharpList::class;
    protected ?string $form = TravelSharpForm::class;
    protected string $label = "Travel";
}