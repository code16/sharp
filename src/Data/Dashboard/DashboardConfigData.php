<?php

namespace Code16\Sharp\Data\Dashboard;

use Code16\Sharp\Data\Commands\ConfigCommandsData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Filters\ConfigFiltersData;

final class DashboardConfigData extends Data
{
    public function __construct(
        public ?ConfigCommandsData $commands = null,
        public ?ConfigFiltersData $filters = null,
    ) {}

    public static function from(array $config): self
    {
        $config = [
            ...$config,
            'commands' => ConfigCommandsData::optional($config['commands'] ?? null),
            'filters' => ConfigFiltersData::optional($config['filters'] ?? null),
        ];

        return new self(...$config);
    }
}
