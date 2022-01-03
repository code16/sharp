<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\SharpProtectedController;
use Code16\Sharp\Show\SharpShow;

abstract class ApiController extends SharpProtectedController
{

    protected function getListInstance(string $entityKey): SharpEntityList
    {
        if(!$listClass = config("sharp.entities.{$entityKey}.list")) {
            throw new SharpInvalidEntityKeyException("The list for the entity [{$entityKey}] was not found.");
        }

        return app($listClass);
    }

    protected function getShowInstance(string $entityKey): SharpShow
    {
        if(!$showClass = config("sharp.entities.{$entityKey}.show")) {
            throw new SharpInvalidEntityKeyException("The show for the entity [{$entityKey}] was not found.");
        }

        return app($showClass);
    }
    
    protected function getFormInstance(string $entityKey): SharpForm
    {
        if($this->isSubEntity($entityKey)) {
            list($entityKey, $subEntityKey) = explode(':', $entityKey);
            $formClass = config("sharp.entities.{$entityKey}.forms.{$subEntityKey}.form");
        } else {
            $formClass = config("sharp.entities.{$entityKey}.form");
        }

        if(!$formClass) {
            throw new SharpInvalidEntityKeyException("The form for the entity [{$entityKey}] was not found.");
        }

        return app($formClass);
    }

    protected function getDashboardInstance(string $dashboardKey): ?SharpDashboard
    {
        if(!$dashboardClass = config("sharp.dashboards.$dashboardKey.view")) {
            throw new SharpInvalidEntityKeyException("The dashboard [{$dashboardKey}] was not found.");
        }

        return app($dashboardClass);
    }

    protected function isSubEntity(string $entityKey): bool
    {
        return str_contains($entityKey, ':');
    }
}