import {FormFieldData, LayoutFieldData} from "@/types";

export type FormFieldProps<Data extends FormFieldData = FormFieldData> = {
    field: Data,
    fieldLayout?: LayoutFieldData,
    fieldErrorKey: string,
    hasError?: boolean,
    value?: Data['value'],
    locale?: string | null,
    root?: boolean
}