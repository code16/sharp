import { ConfigFiltersData } from "@/types";
import { FilterManager } from "./FilterManager";

export function useFilters(filters?: ConfigFiltersData) {
    return new FilterManager(filters);
}
