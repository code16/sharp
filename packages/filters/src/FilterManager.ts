import { FilterData, ConfigFiltersData } from "@/types";
import { filterQueryKey, getFiltersValuesFromQuery } from "./util/query";
import { parseRange, serializeRange } from "@/utils/querystring";
import isEqual from "lodash/isEqual";
import { reactive } from "vue";
import { FilterQueryParams, FilterValues, ParsedValue, SerializedValue } from "./types";




export class FilterManager {
    filters?: ConfigFiltersData;

    state = reactive({
        values: {} as FilterValues,
    })

    constructor(filters?: ConfigFiltersData) {
        this.filters = filters;
        this.values = this.#defaultValues(this.#allFilters);
    }

    get rootFilters(): Array<FilterData> {
        return this.filters?._root ?? [];
    }

    get values() { return this.state.values; }
    set values(values: FilterValues) { this.state.values = values; }

    setValuesFromQuery(query: Partial<FilterQueryParams>) {
        const queryValues = getFiltersValuesFromQuery(query);
        this.values = Object.fromEntries(
            this.#allFilters.map(filter =>
                [
                    filter.key,
                    this.#resolveFilterValue(
                        filter,
                        queryValues[filter.key],
                    ),
                ]
            )
        );
    }

    isValuated(filters: Array<FilterData>): boolean {
        return !isEqual(
            this.getQueryParams(
                Object.fromEntries(filters.map(filter => [filter.key, this.values[filter.key]]))
            ),
            this.getQueryParams(this.#defaultValues(filters))
        );
    }

    getQueryParams(values: FilterValues): FilterQueryParams {
        return Object.entries(values)
            .reduce((res, [key, value]) => ({
                ...res,
                [filterQueryKey(key)]: this.#serializeValue(this.#filter(key), value),
            }), {});
    }

    nextQuery(filter: FilterData, value: FilterData['value']): FilterQueryParams {
        return this.getQueryParams(this.#nextValues(filter, value));
    }

    defaultQuery(filters: FilterData[]): FilterQueryParams {
        return this.getQueryParams(this.#defaultValues(filters));
    }

    get #allFilters(): Array<FilterData> {
        return Object.values(this.filters ?? {}).flat();
    }

    #nextValues(filter: FilterData, value: ParsedValue): FilterValues {
        if(filter.type === 'select' && filter.master) {
            return {
                ...Object.fromEntries(Object.entries(this.values).map(([key, value]) => [key, null])),
                [filter.key]: value,
            };
        }

        return { ...this.values, [filter.key]: value };
    }

    #defaultValues(filters: FilterData[]) {
        return Object.fromEntries(
            filters.map(filter => [filter.key, filter.default])
        );
    }

    #resolveFilterValue(filter: FilterData, value: SerializedValue): ParsedValue {
        if(value == null) {
            // @ts-ignore
            return filter.default;
        }
        if(filter.type === 'select' && filter.multiple && !Array.isArray(value)) {
            return [value];
        }
        if(filter.type === 'daterange') {
            return parseRange(value);
        }
        if(filter.type === 'check') {
            return value === '1';
        }
        return value;
    }

    #serializeValue(filter: FilterData | null, value: ParsedValue): SerializedValue {
        if(!filter) {
            // @ts-ignore
            return value;
        }
        if(filter.type === 'select' && filter.multiple && !(value as string[])?.length) {
            return null;
        }
        if(filter.type === 'daterange') {
            return serializeRange(value);
        }
        if(filter.type === 'check') {
            return value ? '1' : null;
        }
        return value as SerializedValue;
    }

    #filter(key: string): FilterData | undefined {
        return this.#allFilters.find((filter) => filter.key === key);
    }
}
