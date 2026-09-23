<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\Commands\Returns\CommandReturn;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Enums\CommandAction;
use Code16\Sharp\Http\Controllers\HandlesEntityListItems;
use Code16\Sharp\Show\SharpShow;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HandlesCommandResult
{
    use HandlesEntityListItems;

    protected function returnCommandResult(
        SharpEntityList|SharpShow|SharpDashboard $commandContainer,
        string $entityKey,
        CommandReturn $commandReturn,
        ?array $additionalData = null
    ): StreamedResponse|JsonResponse {
        if ($commandReturn->isAction(CommandAction::Download)) {
            return Storage::disk($commandReturn->getDiskName())
                ->download(
                    $commandReturn->getFilePath(),
                    $commandReturn->getFileName(),
                );
        }

        if ($commandReturn->isAction(CommandAction::StreamDownload)) {
            return response()->streamDownload(
                function () use ($commandReturn) {
                    echo $commandReturn->getFileContent();
                },
                $commandReturn->getFileName(),
            );
        }

        $returnedValue = [
            ...$commandReturn->toArray(),
            ...$additionalData ?? [],
        ];

        if ($commandReturn->isAction(CommandAction::Refresh) && $commandContainer instanceof SharpEntityList) {
            // We have to load and build items from ids
            $returnedValue['items'] = $this->addMetaToItems(
                $commandContainer
                    ->updateQueryParamsWithSpecificIds($commandReturn->getItems())
                    ->data()['items'],
                $entityKey,
                $commandContainer,
            );
        }

        return response()->json($returnedValue);
    }
}
