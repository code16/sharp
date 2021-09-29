<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class DashboardCommandController extends ApiController
{
    use HandleCommandReturn;

    /**
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     */
    public function show(string $entityKey, string $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();
        $commandHandler = $this->getCommandHandler($dashboard, $commandKey);

        return response()->json([
            "data" => $commandHandler->formData()
        ]);
    }

    /**
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     */
    public function update(string $entityKey, string $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();
        $commandHandler = $this->getCommandHandler($dashboard, $commandKey);

        return $this->returnCommandResult(
            $dashboard,
            $commandHandler->execute(
                DashboardQueryParams::create()->fillWithRequest("query"),
                $commandHandler->formatRequestData((array)request("data"))
            )
        );
    }

    /**
     * @return \Code16\Sharp\Dashboard\Commands\DashboardCommand|null
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     */
    protected function getCommandHandler(SharpDashboard $dashboard, string $commandKey)
    {
        $commandHandler = $dashboard->dashboardCommandHandler($commandKey);

        if(! $commandHandler->authorize()) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}