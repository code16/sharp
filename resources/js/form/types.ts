import { FormFieldData, LayoutFieldData } from "@/types";

export type FieldMeta = {
    locale?: string,
    uploading?: boolean,
    uploaded: true,
};
export type FieldsMeta = { [key: string]: FieldMeta };

export type FormFieldProps<Data extends FormFieldData = FormFieldData> = {
    field: Data,
    fieldLayout?: LayoutFieldData,
    fieldErrorKey: string,
    hasError?: boolean,
    value?: Data['value'],
    locale?: string | null,
    root?: boolean
}
