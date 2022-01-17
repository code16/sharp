<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Http\Api\ApiController;
use Code16\Sharp\Show\SharpSingleShow;

class ShowInstanceCommandController extends ApiController
{
    use HandleCommandReturn;

    /**
     * Display the Command form.
     */
    public function show(string $entityKey, string $commandKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowPage($entityKey, $instanceId);
        $commandHandler = $this->getInstanceCommandHandler($showPage, $commandKey, $instanceId);

        return response()->json([
            'data' => $commandHandler->formData($instanceId),
        ]);
    }

    /**
     * Execute the Command.
     */
    public function update(string $entityKey, string $commandKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowPage($entityKey, $instanceId);
        $commandHandler = $this->getInstanceCommandHandler($showPage, $commandKey, $instanceId);

        return $this->returnCommandResult(
            $showPage,
            $commandHandler->execute(
                $instanceId,
                $commandHandler->formatRequestData((array) request('data'), $instanceId)
            )
        );
    }

    private function getShowPage(string $entityKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowInstance($entityKey);

        abort_if(
            (!$instanceId && !$showPage instanceof SharpSingleShow)
            || ($instanceId && $showPage instanceof SharpSingleShow),
            404
        );

        $showPage->buildShowConfig();

        return $showPage;
    }
}
