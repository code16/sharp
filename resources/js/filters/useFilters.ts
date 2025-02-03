import { ConfigFiltersData, FilterValuesData } from "@/types";
import { FilterManager } from "./FilterManager";
import { FilterValues } from "@/filters/types";

export function useFilters(filters?: ConfigFiltersData, filterValues?: FilterValuesData) {
    return new FilterManager(filters, filterValues);
}
