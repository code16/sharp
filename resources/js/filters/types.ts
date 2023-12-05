import { FilterData } from "@/types";

export type ParsedValue = FilterData['value'] | null;
export type SerializedValue = string | string[] | null;
export type FilterValues = Record<string, ParsedValue>;
export type FilterQueryParams = Record<string, SerializedValue>;
