import { LayoutFieldData, ShowFieldData } from "@/types";

export type ShowFieldProps<Data extends ShowFieldData = ShowFieldData> = {
    field: Data,
    value: Data['value'],
    fieldLayout?: LayoutFieldData,
    locale: string,
    collapsable?: boolean,
    root?: boolean,
}
