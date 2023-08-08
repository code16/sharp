<?php

namespace Code16\Sharp\Data\Dashboard;


use Code16\Sharp\Data\Dashboard\Widgets\WidgetData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;

final class DashboardData extends Data
{
    public function __construct(
        /** @var DataCollection<WidgetData> */
        public DataCollection $widgets,
        public DashboardConfigData $config,
        public DashboardLayoutData $layout,
        /** @var array<string,mixed> */
        public array $data,
        /** @var array<string,mixed> */
        public array $fields,
    ) {
    }

    public static function from(array $dashboard): self
    {
        return new self(
            widgets: WidgetData::collection($dashboard['widgets']),
            config: DashboardConfigData::from($dashboard['config']),
            layout: DashboardLayoutData::from($dashboard['layout']),
            data: $dashboard['data'],
            fields: $dashboard['fields'],
        );
    }
}
