import { LayoutFieldData, ShowFieldData } from "@/types";

export type ShowFieldProps<Data extends ShowFieldData = ShowFieldData, Value = Data['value']> = {
    field: Omit<Data, 'value'>,
    value?: Value,
    fieldLayout?: LayoutFieldData,
    locale?: string,
    collapsable?: boolean,
    root?: boolean,
    isRightCol?: boolean,
    hideLabel?: boolean,
    row?: LayoutFieldData[],
}
