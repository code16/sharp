<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Form\SharpForm;
use Illuminate\Routing\Controller;

abstract class ApiController extends Controller
{

    /**
     * @param string $entityKey
     * @return SharpEntityList
     */
    protected function getListInstance(string $entityKey)
    {
        return app(config("sharp.entities.{$entityKey}.list"));
    }

    /**
     * @param string $entityKey
     * @return SharpForm|null
     */
    protected function getFormInstance(string $entityKey)
    {
        $formClass = config("sharp.entities.{$entityKey}.form");

        return $formClass ? app($formClass) : null;
    }

    /**
     * @return SharpDashboard|null
     */
    protected function getDashboardInstance()
    {
        $dashboardClass = config("sharp.dashboard");

        return $dashboardClass ? app($dashboardClass) : null;
    }
}