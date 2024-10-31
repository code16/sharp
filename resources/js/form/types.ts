import {
    FormAutocompleteDynamicLocalValuesData,
    FormAutocompleteLocalFieldData,
    FormDynamicOptionsData,
    FormFieldData,
    FormSelectFieldData,
    LayoutFieldData
} from "@/types";

export type FieldMeta = {
    locale?: string,
    uploading?: boolean,
    uploaded: true,
};
export type FieldsMeta = { [key: string]: FieldMeta };

export type WithDynamicAttributesApplied<Data extends FormFieldData> =
    Data extends FormSelectFieldData
        ? Omit<FormSelectFieldData, 'options'> & { options: Exclude<FormSelectFieldData['options'], FormDynamicOptionsData> }
        : Data extends FormAutocompleteLocalFieldData
            ? Omit<FormAutocompleteLocalFieldData, 'localValues'> & { localValues: Exclude<FormAutocompleteLocalFieldData['localValues'], FormAutocompleteDynamicLocalValuesData> }
            : Data;

export type FormFieldProps<Data extends FormFieldData = FormFieldData & { value: any }, Value = Data['value']> = {
    field: WithDynamicAttributesApplied<Data>,
    fieldLayout?: LayoutFieldData,
    fieldErrorKey?: string,
    hasError?: boolean,
    value?: Value,
    locale?: string | null,
    root?: boolean,
    row?: LayoutFieldData[],
}

export type FormFieldEmitInputOptions = { error?: string, force?: boolean };

export type FormFieldEmits<Data extends FormFieldData> = {
    (e: 'input', value: Data['value'], options?: FormFieldEmitInputOptions): void
    (e: 'error', error: string): void
    (e: 'clear'): void
}
