import { FilterData, ConfigFiltersData, FilterValuesData } from "@/types";
import { reactive } from "vue";
import type {  FilterValues, ParsedValue } from "./types";


export class FilterManager {
    filters?: ConfigFiltersData;
    state = reactive<{
        filterValues: FilterValuesData | null,
    }>({
        filterValues: null,
    })

    constructor(filters?: ConfigFiltersData, filterValues?: FilterValuesData) {
        this.update(filters, filterValues);
    }

    get filterValues() { return this.state.filterValues }
    set filterValues(filterValues: FilterValuesData) { this.state.filterValues = filterValues }
    get currentValues() { return this.filterValues?.current ?? {}; }

    get rootFilters(): Array<FilterData> {
        return this.filters?._root ?? [];
    }

    update(filters?: ConfigFiltersData, filterValues?: FilterValuesData) {
        this.filters = filters;
        this.state.filterValues = filterValues;
    }

    isValuated(filters: Array<FilterData>): boolean {
        return filters.some(filter => this.filterValues.valuated[filter.key]);
    }

    nextValues(filter: FilterData, value: ParsedValue): FilterValues {
        if(filter.type === 'select' && filter.master) {
            return {
                ...Object.fromEntries(Object.entries(this.currentValues).map(([key, value]) => [key, null])),
                [filter.key]: value,
            };
        }

        return { ...this.currentValues, [filter.key]: value };
    }

    defaultValues(filters: FilterData[]) {
        return Object.fromEntries(
            filters.map(filter => [filter.key, this.filterValues.default[filter.key]])
        );
    }
}
