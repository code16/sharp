import { FilterData, ConfigFiltersData } from "@/types";
import { getFiltersQueryParams, getFiltersValuesFromQuery } from "./util/query";
import { parseRange, serializeRange } from "@/utils/querystring";
import isEqual from "lodash/isEqual";

type ParsedValue = FilterData['value'] | null;
type SerializedValue = string | string[] | null;


export class FilterManager {
    filters: ConfigFiltersData;
    values: Record<string, ParsedValue> = {};

    // state = reactive({
    //     values: {} as Record<string, ParsedValue>,
    //     filters: null as ConfigFiltersData,
    // })
    //
    // get values() { return this.state.values; }
    // set values(values) { this.state.values = values; }
    //
    // get filters() { return this.state.filters; }
    // set filters(filters) { this.state.filters = filters; }

    constructor(filters?: ConfigFiltersData) {
        this.filters = filters;
        this.values = this.#defaultValues(this.#allFilters);
    }

    get rootFilters(): Array<FilterData> {
        return this.filters?._root ?? [];
    }

    setValuesFromQuery(query: Record<string, SerializedValue>) {
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

    getQueryParams(values: Record<string, ParsedValue>): Record<string, SerializedValue> {
        // @ts-ignore
        return getFiltersQueryParams(values, (value, key) =>
            this.#serializeValue(this.#filter(key), value)
        );
    }

    nextQuery(filter, value): Record<string, SerializedValue> {
        return this.getQueryParams(this.#nextValues(filter, value));
    }

    defaultQuery(filters: FilterData[]): Record<string, SerializedValue> {
        return this.getQueryParams(this.#defaultValues(filters));
    }

    get #allFilters(): Array<FilterData> {
        return Object.values(this.filters ?? {}).flat();
    }

    #nextValues(filter: FilterData, value: ParsedValue): Record<string, ParsedValue> {
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

    #serializeValue(filter: FilterData, value: ParsedValue): SerializedValue {
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
