import {
    ConfigCommandsData, DashboardData, DashboardLayoutSectionData, FilterData,
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

    _sectionFilters: Record<string, FilterData[]> = {};

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

    get visibleFilters(): Array<FilterData>|null {
        return this.hiddenFilters
            ? this.config.filters?._root.filter(filter => !(filter.key in this.hiddenFilters))
            : this.config.filters?._root;
    }

    get visibleCommands(): ConfigCommandsData {
        return Object.fromEntries(
            Object.entries(this.config.commands ?? {}).map(([key, commands]) => [
                key,
                commands.map(group => group.filter(command => {
                    return command.authorization && !this.hiddenCommands?.includes(command.key);
                }))
            ])
        );
    }

    sectionVisibleFilters(section: DashboardLayoutSectionData) {
        if(this.hiddenFilters) {
            return this._sectionFilters[section.key] ??= (
                this.config.filters?.[section.key]?.filter(filter => !(filter.key in this.hiddenFilters))
            );
        }

        return this.config.filters?.[section.key];
    }
}
