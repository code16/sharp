<?php

namespace Code16\Sharp\Data\Dashboard;


use Code16\Sharp\Data\Commands\ConfigCommandsData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Filters\ConfigFiltersData;
use Code16\Sharp\Data\PageAlertConfigData;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class DashboardConfigData extends Data
{
    public function __construct(
        #[Optional]
        public ?ConfigCommandsData $commands = null,
        #[Optional]
        public ?ConfigFiltersData $filters = null,
        #[Optional]
        public ?PageAlertConfigData $globalMessage = null,
    ) {
    }

    public static function from(array $config): self
    {
        $config = [
            ...$config,
            'commands' => isset($config['commands'])
                ? ConfigCommandsData::from($config['commands'])
                : null,
            'filters' => isset($config['filters'])
                ? ConfigFiltersData::from($config['filters'])
                : null,
            'globalMessage' => isset($config['globalMessage'])
                ? PageAlertConfigData::from($config['globalMessage'])
                : null,
        ];

        return new self(...$config);
    }
}
