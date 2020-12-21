<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\DashboardDateRangeFilter;
use Code16\Sharp\Dashboard\DashboardFilter;
use Code16\Sharp\Tests\SharpTestCase;

class DashboardFilterTest extends SharpTestCase
{

    /** @test */
    function we_can_get_dashboard_filters_config()
    {
        $dashboard = new class extends SharpDashboardTestDashboard {
            function buildDashboardConfig()
            {
                $this->addFilter("test", new class implements DashboardFilter {
                    public function values() { return [1 => "A", 2 => "B"]; }
                });
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertArraySubset([
            "filters" => [
                [
                    "key" => "test",
                    "label" => "test",
                    "multiple" => false,
                    "required" => false,
                    "values" => [
                        ["id" => 1, "label" => "A"],
                        ["id" => 2, "label" => "B"]
                    ]
                ]
            ]
        ], $dashboard->dashboardConfig());
    }

    /** @test */
    function we_can_get_dashboard_date_range_filter_config()
    {
        $dashboard = new class extends SharpDashboardTestDashboard {
            function buildDashboardConfig()
            {
                $this->addFilter("test", new class implements DashboardDateRangeFilter {
                    public function values() { return []; }
                });
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertArraySubset([
            "filters" => [
                [
                    "key" => "test",
                    "type" => "daterange",
                    "label" => "test",
                    "required" => false,
                ]
            ]
        ], $dashboard->dashboardConfig());
    }

    // Note: all other filters test already are in EntityListFilterTest
}