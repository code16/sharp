<?php

namespace App\Sharp\Entities;

use App\Sharp\PilotJuniorSharpForm;
use App\Sharp\PilotJuniorSharpValidator;
use App\Sharp\PilotSharpList;
use App\Sharp\PilotSharpShow;
use Code16\Sharp\Utils\Entities\SharpDefaultEntity;

class PilotEntity extends SharpDefaultEntity
{
    public function getList(): ?string
    {
        return PilotSharpList::class;
    }

    public function getShow(): ?string
    {
        return PilotSharpShow::class;
    }
    
    public function getForm(): ?string
    {
        return PilotJuniorSharpForm::class;
        
        return match ($subEntity) {
            "junior" => \App\Sharp\PilotJuniorSharpForm::class,
            "senior" => \App\Sharp\PilotSeniorSharpForm::class,
        };
    }

    protected function getLabel(): ?string
    {
        return "Pilot";
    }
    
    protected function getFormValidator(): ?string
    {
        return PilotJuniorSharpValidator::class;
    }
}