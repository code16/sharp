<?php

namespace Code16\Sharp\Data\Dashboard;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\PageAlertConfigData;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class DashboardConfigData extends Data
{
    public function __construct(
        #[Optional]
        public ?PageAlertConfigData $globalMessage = null,
    ) {
    }

    public static function from(array $config): self
    {
        $config = [
//            ...$config,
            'globalMessage' => isset($config['globalMessage'])
                ? PageAlertConfigData::from($config['globalMessage'])
                : null,
        ];

        return new self(...$config);
    }
}
