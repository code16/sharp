<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class RequestFieldContainerData extends Data
{
    public function __construct(
        public ?string $embed_key,
        public ?string $entity_list_command_key,
        public ?string $show_command_key,
        public ?string $dashboard_command_key,
        public string|int|null $instance_id,
    ) {}

    public static function from(array $request): self
    {
        return new self(
            embed_key: $request['embed_key'] ?? null,
            entity_list_command_key: $request['entity_list_command_key'] ?? null,
            show_command_key: $request['show_command_key'] ?? null,
            dashboard_command_key: $request['dashboard_command_key'] ?? null,
            instance_id: $request['instance_id'] ?? null,
        );
    }
}
