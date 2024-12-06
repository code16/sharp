import { FilterData } from "@/types";

export type ParsedValue = FilterData['value'] | null;
export type SerializedValue = string | string[] | null;
export type FilterValues = Record<string, ParsedValue>;
export type FilterQueryParams = Record<string, SerializedValue>;

export type FilterProps<Data extends FilterData, Value = Data['value']> = {
    filter: Omit<Data, 'value'>;
    value: Value;
    valuated: boolean;
    disabled?: boolean;
    inline?: boolean;
}
export type FilterEmits<Data extends FilterData, Value = Data['value']> = {
    (e: 'input', values: Value): void
};
