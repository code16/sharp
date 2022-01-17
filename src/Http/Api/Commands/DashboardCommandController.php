<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class DashboardCommandController extends ApiController
{
    use HandleCommandReturn;

    public function show(string $entityKey, string $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();

        $commandHandler = $this->getCommandHandler($dashboard, $commandKey);

        return response()->json([
            'data' => $commandHandler->formData(),
        ]);
    }

    public function update(string $entityKey, string $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();

        $commandHandler = $this->getCommandHandler($dashboard, $commandKey);

        return $this->returnCommandResult(
            $dashboard,
            $commandHandler->execute(
                $commandHandler->formatRequestData((array) request('data'))
            )
        );
    }

    protected function getCommandHandler(SharpDashboard $dashboard, string $commandKey)
    {
        if ($handler = $dashboard->findDashboardCommandHandler($commandKey)) {
            $handler->initQueryParams(DashboardQueryParams::create()->fillWithRequest('query'));
        }

        if (!$handler->authorize()) {
            throw new SharpAuthorizationException();
        }

        return $handler;
    }
}
