<?php

namespace Code16\Sharp\Data\Commands;


use Code16\Sharp\Data\Dashboard\DashboardConfigData;
use Code16\Sharp\Data\Dashboard\Widgets\WidgetData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Enums\CommandType;
use Illuminate\Contracts\Support\Arrayable;
use Spatie\TypeScriptTransformer\Attributes\RecordTypeScriptType;

#[RecordTypeScriptType(CommandType::class.'|string', 'array<DataCollection<'.CommandData::class.'>>')]
final class ConfigCommandsData implements Arrayable
{
    public function __construct(
        protected array $commands,
    ) {
    }

    public static function from(array $configCommands): self
    {
        return new self(
            collect($configCommands)
                ->map(fn (array $commands) => collect($commands)
                    ->map(fn (array $commands) => CommandData::collection($commands))
                )
                ->all()
        );
    }

    public function toArray(): array
    {
        return $this->commands;
    }
}
