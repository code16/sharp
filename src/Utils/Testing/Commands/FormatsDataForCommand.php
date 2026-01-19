<?php

namespace Code16\Sharp\Utils\Testing\Commands;

use Code16\Sharp\EntityList\Commands\Command;

trait FormatsDataForCommand
{
    protected function formatDataForCommand(Command $commandHandler, array $data, ?array $baseData = null): array
    {
        return [
            ...$baseData ?: [],
            ...collect($commandHandler->applyFormatters($data))
                ->when($baseData)->only(array_keys($data))
                ->all(),
        ];
    }
}
