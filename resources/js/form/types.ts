import {
    FormAutocompleteDynamicLocalValuesData,
    FormAutocompleteLocalFieldData,
    FormDynamicOptionsData,
    FormFieldData, FormListFieldData,
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
        ? Omit<FormSelectFieldData, 'options' | 'value'> & { options: Exclude<FormSelectFieldData['options'], FormDynamicOptionsData> }
        : Data extends FormAutocompleteLocalFieldData
            ? Omit<FormAutocompleteLocalFieldData, 'localValues' | 'value'> & { localValues: Exclude<FormAutocompleteLocalFieldData['localValues'], FormAutocompleteDynamicLocalValuesData> }
            : Omit<Data, 'value'>;

export type FormFieldProps<Data extends FormFieldData = FormFieldData, Value = Data['value']> = {
    field: WithDynamicAttributesApplied<Data>,
    parentField?: FormListFieldData
    fieldLayout?: LayoutFieldData,
    fieldErrorKey?: string,
    parentData?: FormFieldData | FormListFieldData['value'][number],
    hasError?: boolean,
    value?: Value,
    locale?: string | null,
    root?: boolean,
    row?: LayoutFieldData[],
}

export type FormFieldEmitInputOptions = {
    error?: string,
    force?: boolean,
    preserveError?: boolean,
    skipRefresh?: boolean,
    shouldRefresh?: boolean,
};

export type FormFieldEmits<Data extends FormFieldData = FormFieldData> = {
    (e: 'input', value: Data['value'], options?: FormFieldEmitInputOptions): void
    (e: 'error', error: string): void
    (e: 'clear'): void
    (e: 'locale-change', locale: string): void
}
