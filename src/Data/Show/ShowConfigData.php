<?php

namespace Code16\Sharp\Data\Show;


use Code16\Sharp\Data\Commands\CommandData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\EntityStateData;
use Code16\Sharp\Data\PageAlertConfigData;
use Code16\Sharp\Enums\CommandType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\RecordTypeScriptType;

final class ShowConfigData extends Data
{
    public function __construct(
        public string $deleteConfirmationText,
        public bool $isSingle = false,
        #[Optional]
        #[RecordTypeScriptType(CommandType::class, 'array<DataCollection<'.CommandData::class.'>>')]
        public ?array $commands = null,
        #[Optional]
        public ?string $multiformAttribute = null,
        #[Optional]
        public ?string $titleAttribute = null,
        #[Optional]
        public ?string $breadcrumbAttribute = null,
        #[Optional]
        public ?EntityStateData $state = null,
        #[Optional]
        public ?PageAlertConfigData $globalMessage = null,
    ) {
    }

    public static function from(array $config): self
    {
        $config = [
            ...$config,
            'state' => isset($config['state'])
                ? EntityStateData::from($config['state'])
                : null,
            'commands' => isset($config['commands'])
                ? collect($config['commands'])
                    ->map(fn (array $commands) => collect($commands)
                        ->map(fn (array $commands) => CommandData::collection($commands))
                    )
                    ->all()
                : null,
            'globalMessage' => isset($config['globalMessage'])
                ? PageAlertConfigData::from($config['globalMessage'])
                : null,
        ];

        return new self(...$config);
    }
}