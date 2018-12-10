<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\SharpProtectedController;

abstract class ApiController extends SharpProtectedController
{

    /**
     * @param string $entityKey
     * @return SharpEntityList
     * @throws SharpInvalidEntityKeyException
     */
    protected function getListInstance(string $entityKey)
    {
        if(! $configKey = config("sharp.entities.{$entityKey}.list")) {
            throw new SharpInvalidEntityKeyException("The entity [{$entityKey}] was not found.");
        }

        return app($configKey);
    }

    /**
     * @param string $entityKey
     * @return SharpForm
     * @throws SharpInvalidEntityKeyException
     */
    protected function getFormInstance(string $entityKey)
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

    /**
     * @param string $dashboardKey
     * @return SharpDashboard|null
     */
    protected function getDashboardInstance(string $dashboardKey)
    {
        $dashboardClass = config("sharp.dashboards.$dashboardKey.view");

        return $dashboardClass ? app($dashboardClass) : null;
    }

    /**
     * @param string $entityKey
     * @return bool
     */
    protected function isSubEntity(string $entityKey): bool
    {
        return strpos($entityKey, ':') !== false;
    }
}