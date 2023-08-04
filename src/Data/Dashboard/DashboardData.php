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
        public array $data = [],
    ) {
    }

    public static function from(array $dashboard): self
    {
        return new self(
            WidgetData::collection($dashboard['widgets']),
            DashboardConfigData::from($dashboard['config']),
            $dashboard['data'],
        );
    }
}
