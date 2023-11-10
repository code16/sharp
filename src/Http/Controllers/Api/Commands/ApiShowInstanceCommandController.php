<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Show\SharpSingleShow;

class ApiShowInstanceCommandController extends ApiController
{
    use HandleCommandReturn, HandleCommandForm;

    public function show(string $entityKey, string $commandKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowPage($entityKey, $instanceId);
        $commandHandler = $this->getInstanceCommandHandler($showPage, $commandKey, $instanceId);
        $formData = $commandHandler->formData($instanceId) ?: null;

        return response()->json(
            CommandFormData::from([
                ...$this->getCommandForm($commandHandler),
                'data' => $formData,
                'pageAlert' => $commandHandler->pageAlert($formData),
            ]),
        );
    }

    public function update(string $entityKey, string $commandKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowPage($entityKey, $instanceId);
        $commandHandler = $this->getInstanceCommandHandler($showPage, $commandKey, $instanceId);

        $formattedData = $commandHandler->formatRequestData(request()->all());
        $commandHandler->validateRequest($formattedData);

        $result = $this->returnCommandResult(
            $showPage,
            $commandHandler->execute(
                $instanceId,
                $commandHandler->formatRequestData((array) request('data'), $instanceId),
            ),
        );

//        if ($filesData = $this->extractUploadedOrUpdatedFiles($formattedData)) {
//            HandlePostedFilesJob::dispatch($filesData, $instanceId);
//        }

        return $result;
    }

    private function getShowPage(string $entityKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowInstance($entityKey);

        abort_if(
            (! $instanceId && ! $showPage instanceof SharpSingleShow)
            || ($instanceId && $showPage instanceof SharpSingleShow),
            404,
        );

        $showPage->buildShowConfig();

        return $showPage;
    }
}
