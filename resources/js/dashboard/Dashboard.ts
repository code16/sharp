import {
    DashboardData, FilterData,
} from "@/types";


export class Dashboard {
    widgets: DashboardData['widgets'];
    config: DashboardData['config'];
    layout: DashboardData['layout'];
    data: DashboardData['data'];
    filterValues: DashboardData['filterValues'];
    pageAlert:DashboardData['pageAlert'];
    query: DashboardData['query'];

    dashboardKey: string;
    hiddenFilters?: Record<string, FilterData['value']>;
    hiddenCommands: string[];

    constructor(
        data: DashboardData,
        dashboardKey: string,
        hiddenFilters?: Record<string, FilterData['value']>,
        hiddenCommands?: string[]
    ) {
        Object.assign(this, data);

        this.dashboardKey = dashboardKey;
        this.hiddenFilters = hiddenFilters;
        this.hiddenCommands = hiddenCommands;
    }
}
