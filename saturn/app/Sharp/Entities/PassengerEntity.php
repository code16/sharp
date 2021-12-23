<?php

namespace App\Sharp\Entities;

use App\Sharp\PassengerSharpForm;
use App\Sharp\PassengerSharpList;
use App\Sharp\PilotSharpShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class PassengerEntity extends SharpEntity
{
    protected ?string $list = PassengerSharpList::class;
    protected ?string $form = PassengerSharpForm::class;
    protected ?string $show = PilotSharpShow::class;
    protected string $label = "Passenger";
}