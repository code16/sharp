import { FilterData } from "@/types";

type ParsedValue = FilterData['value'] | null;
type SerializedValue = string | string[] | null;

export type FilterValues = Record<string, ParsedValue>;
export type FilterQueryParams = Record<string, SerializedValue>;
