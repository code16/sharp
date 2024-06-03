import {
    CommandFormData,
    FormData,
    FormEditorFieldData,
    FormFieldData,
    FormLayoutFieldsetData,
    FormLayoutTabData,
    FormListFieldData,
    LayoutFieldData
} from "@/types";
import { computeCondition } from "./util/conditional-display";
import { reactive } from "vue";
import { transformFields } from "./util";
import { FieldMeta, FieldsMeta, WithDynamicAttributesApplied, WithFieldsMeta } from "./types";
import get from 'lodash/get';
import set from 'lodash/set';


export class Form implements FormData, CommandFormData {
    authorizations: FormData['authorizations'];
    config: FormData['config'] & CommandFormData['config'];
    fields: FormData['fields'];
    layout: FormData['layout'];
    locales: FormData['locales'];
    pageAlert: FormData['pageAlert'];

    state = reactive<{
        data: WithFieldsMeta<FormData['data']>,
        meta: { [fieldKey: string]: FieldMeta | Array<FieldsMeta> },
        errors: { [key: string]: string | string[] }
    }>({
        data: {},
        meta: {},
        errors: {},
    });

    serializedData: FormData['data'];

    entityKey: string;
    instanceId: string | number;

    constructor(data: FormData, entityKey: string, instanceId: string | number) {
        Object.assign(this, data);
        this.entityKey = entityKey;
        this.instanceId = instanceId;
        this.serializedData = this.data;
    }

    get data() {
        return this.state.data ?? {};
    }
    set data(data) {
        this.state.data = data;
    }

    get errors() {
        return this.state.errors;
    }
    set errors(errors) {
        this.state.errors = errors;
    }

    get meta() {
        return this.state.meta;
    }
    set meta(meta) {
        this.state.meta = meta;
    }

    get allFieldsMeta(): FieldMeta[] {
        return Object.values(this.meta)
            .map(fieldMeta => Array.isArray(fieldMeta)
                ? fieldMeta.map(meta => Object.values(meta))
                : fieldMeta
            )
            .flat(2);
    }

    get canEdit(): boolean {
        if(!this.authorizations) {
            return true;
        }

        if(this.config.isSingle || this.instanceId) {
            return this.authorizations.update;
        }

        return this.authorizations.create;
    }

    get isReadOnly(): boolean {
        return !this.canEdit;
    }

    get currentLocale(): string|null {
        const selectedLocales = [...new Set(this.allFieldsMeta.map(fieldMeta => fieldMeta.locale).filter(Boolean))];
        if(!selectedLocales.length) {
            return this.locales?.[0] ?? null;
        }
        if(selectedLocales.length === 1) {
            return selectedLocales[0];
        }
        return null;
    }

    get defaultLocale(): string|null {
        return this.currentLocale ?? this.locales?.[0];
    }

    get localized(): boolean { // needed ?
        return this.locales?.length > 0;
    }

    get isUploading(): boolean {
        return this.allFieldsMeta.some(fieldMeta => fieldMeta.uploading);
    }

    getMeta(fieldKey: string): FieldMeta | undefined {
        return get(this.meta, fieldKey);
    }

    setMeta(fieldKey: string, values: Partial<FieldMeta> | FieldsMeta[]) {
        if(Array.isArray(values)) {
            this.meta[fieldKey] = values;
        } else {
            set(this.meta, fieldKey, {
                ...get(this.meta, fieldKey),
                ...values,
            });
        }
    }

    setAllMeta(values: Partial<FieldMeta>, meta = this.meta) {
        Object.values(meta).forEach(fieldMeta => {
            if(Array.isArray(fieldMeta)) {
                fieldMeta.forEach(itemFieldsMeta => {
                    this.setAllMeta(values, itemFieldsMeta);
                });
            } else {
                Object.assign(fieldMeta, values);
            }
        });
    }

    setErrors(errors: { [key: string]: string }, data = this.data, fields = this.fields) {
        Object.entries(fields)
            .filter(([fieldKey]) => fieldKey in errors)
            .map(([fieldKey, field]) => {
                data._meta = {
                    ...data._meta,
                    errors: Object.fromEntries(
                        Object.entries(errors)
                            .filter(([key]) =>
                                [fieldKey, ...('localized' in field && field.localized ? this.locales.map(locale => `${fieldKey}.${locale}`) : [])]
                                    .includes(key)
                            )
                    ),
                };

                if(field.type === 'list') {
                    (data[fieldKey] as FormListFieldData['value']).forEach((item, index) => {
                        const itemErrors = Object.fromEntries(
                            Object.entries(errors)
                                .filter(([key]) => key.startsWith(`${fieldKey}.${index}.`))
                                .map(([key, error]) => [key.slice(`${fieldKey}.${index}.`.length), error])
                        );
                        this.setErrors(itemErrors, item, field.itemFields);
                    });
                }
            });
    }

    setError(key: string, error: string) {
        this.errors[key] = error;
    }

    clearError(key: string) {
        delete this.errors[key];
    }

    clearErrors(baseKey: string) {
        this.errors = Object.fromEntries(
            Object.entries(this.errors)
                .filter(([key]) => !key.startsWith(baseKey+'.'))
        );
    }

    getField(key: string, fields = this.fields, data = this.data, readOnly = false): WithDynamicAttributesApplied<FormFieldData> {
        const fieldsWithDynamicAttributesApplied = transformFields(fields, data);

        return {
            ...fieldsWithDynamicAttributesApplied[key],
            readOnly: this.isReadOnly || fieldsWithDynamicAttributesApplied[key].readOnly || readOnly,
        };
    }

    fieldShouldBeVisible(fieldLayout: LayoutFieldData, fields = this.fields, data = this.data) {
        const field = fields[fieldLayout.key];

        if(!field.conditionalDisplay) {
            return true;
        }
        const fieldsWithAppliedDynamicAttributes = transformFields(fields, data);

        return computeCondition(fieldsWithAppliedDynamicAttributes, data, field.conditionalDisplay);
    }

    fieldsetShouldBeVisible(fieldset: FormLayoutFieldsetData, fields = this.fields, data = this.data) {
        return fieldset.fields
            .flat(2)
            .some(fieldLayout => this.fieldShouldBeVisible(fieldLayout, fields, data));
    }

    fieldError(key: string): string | undefined {
        return Array.isArray(this.errors[key])
            ? this.errors[key][0]
            : this.errors[key] as string;
    }

    fieldHasError(field: FormFieldData, key: string, locale = null, includeChildren = false): boolean {
        if(this.fieldError(key)) {
            return true;
        }
        if('localized' in field && field.localized) {
            return locale
                ? this.fieldLocalesContainingError(key).includes(locale)
                : this.fieldLocalesContainingError(key).length > 0;
        }
        if(includeChildren) {
            if(field.type === 'list') {
                return (this.data[key] as FormListFieldData['value'])?.some((item, index) =>
                    Object.keys(item).some(fieldKey => this.fieldHasError(field, `${key}.${index}.${fieldKey}`))
                );
            }
        }
        return false;
    }

    fieldLocalesContainingError(key: string): string[] {
        return this.locales.filter(locale => this.fieldError(`${key}.${locale}`));
    }

    fieldIsEmpty(field: FormFieldData, value: FormFieldData['value'], locale: string): boolean {
        if('localized' in field && field.localized) {
            if(field.type === 'editor') {
                return !(value as FormEditorFieldData['value'])?.text?.[locale];
            }
            if(field.type === 'text' || field.type === 'textarea') {
                return !value?.[locale];
            }
        }
        return !value;
    }

    tabErrorsCount(tab: FormLayoutTabData): number {
        const tabFields = tab.columns
            .map(col =>
                col.fields.flat(2).map(fieldLayout =>
                    'legend' in fieldLayout ? fieldLayout.fields.flat(2) : fieldLayout
                )
            )
            .flat(2)
            .map(fieldLayout => this.fields[fieldLayout.key])
            .filter(Boolean);

        return tabFields.filter(field => this.fieldHasError(field, field.key, null, true)).length;
    }
}
