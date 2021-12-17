<?php

namespace App\Sharp\Entities;

use App\Sharp\PilotSharpList;
use App\Sharp\PilotSharpShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class PilotEntity extends SharpEntity
{
    protected ?string $list = PilotSharpList::class;
    protected ?string $show = PilotSharpShow::class;
    protected string $label = "Pilot";
    
    public function getMultiforms(): array
    {
        return [
            "junior" => [\App\Sharp\PilotJuniorSharpForm::class, "Junior pilot"],
            "senior" => [\App\Sharp\PilotSeniorSharpForm::class, "Senior pilot"],
        ];
    }
}