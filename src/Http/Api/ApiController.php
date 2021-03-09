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
        if(! $configKey = config("sharp.entities.{$entityKey}.list")) {
            throw new SharpInvalidEntityKeyException("The entity [{$entityKey}] was not found.");
        }

        return app($configKey);
    }

    protected function getShowInstance(string $entityKey): SharpShow
    {
        if($this->isSubEntity($entityKey)) {
            list($entityKey, $subEntityKey) = explode(':', $entityKey);
            $showClass = config("sharp.entities.{$entityKey}.shows.{$subEntityKey}.show");

        } else {
            $showClass = config("sharp.entities.{$entityKey}.show");
        }

        if(! $showClass) {
            throw new SharpInvalidEntityKeyException("The entity [{$entityKey}] was not found.");
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

        if(! $formClass) {
            throw new SharpInvalidEntityKeyException("The entity [{$entityKey}] was not found.");
        }

        return app($formClass);
    }

    protected function getDashboardInstance(string $dashboardKey): ?SharpDashboard
    {
        $dashboardClass = config("sharp.dashboards.$dashboardKey.view");

        return $dashboardClass ? app($dashboardClass) : null;
    }

    protected function isSubEntity(string $entityKey): bool
    {
        return strpos($entityKey, ':') !== false;
    }
}