import {
    CommandFormData,
    EmbedFormData,
    FormData,
    FormEditorFieldData,
    FormFieldData,
    FormLayoutColumnData,
    FormLayoutFieldsetData,
    FormLayoutTabData,
    FormListFieldData,
    LayoutFieldData
} from "@/types";
import { computeCondition } from "./util/conditional-display";
import { reactive } from "vue";
import { transformFields } from "./util";
import { FieldMeta, FieldsMeta, WithDynamicAttributesApplied } from "./types";
import get from 'lodash/get';
import set from 'lodash/set';
import { config } from "@/utils/config";

export type FormEvents = 'error';

export class Form implements FormData, CommandFormData, EventTarget {
    authorizations: FormData['authorizations'];
    config: FormData['config'] & CommandFormData['config'];
    fields: FormData['fields'];
    layout: FormData['layout'];
    locales: FormData['locales'];
    pageAlert: FormData['pageAlert'];
    title: FormData['title'];

    state = reactive<{
        data: FormData['data'],
        meta: { [fieldKey: string]: FieldMeta | Array<FieldsMeta> },
        errors: { [key: string]: string | string[] }
    }>({
        data: {},
        meta: {},
        errors: {},
    });

    serializedData: FormData['data'];

    entityKey: string;
    instanceId: string | number | null;
    embedKey?: string;
    commandKey?: string;

    constructor(
        data: FormData | EmbedFormData,
        entityKey: string,
        instanceId: string | number | null,
        additionalProps?: { embedKey?: string, commandKey?: string }
    ) {
        Object.assign(this, data);
        this.entityKey = entityKey;
        this.instanceId = instanceId;
        this.embedKey = additionalProps?.embedKey;
        this.commandKey = additionalProps?.commandKey;
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
    get hasErrors()
    {
        return Object.keys(this.errors).length > 0;
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

    getMeta(fieldKey: string) {
        return get(this.meta, fieldKey);
    }

    setMeta(fieldKey: string, values: Partial<FieldMeta> | FieldsMeta[], listFieldKey?: string) {
        if(listFieldKey && !this.meta[listFieldKey]) {
            this.meta[listFieldKey] = [];
        }
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

    onError(errors: { [key: string]: string | string[] }) {
        this.errors = errors;
        this.dispatchEvent(new Event('error'));
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

        return fieldsWithDynamicAttributesApplied[key]
            ? {
                ...fieldsWithDynamicAttributesApplied[key],
                readOnly: this.isReadOnly || fieldsWithDynamicAttributesApplied[key].readOnly || readOnly,
            }
            : null;
    }

    fieldRowShouldBeVisible(row: FormLayoutColumnData['fields'][0], fields = this.fields, data = this.data) {
        return row.some(fieldLayout =>
            'legend' in fieldLayout
                ? this.fieldsetShouldBeVisible(fieldLayout, fields, data)
                : this.fieldShouldBeVisible(fieldLayout, fields, data)
        );
    }

    fieldShouldBeVisible(fieldLayout: LayoutFieldData, fields = this.fields, data = this.data) {
        const field = fields[fieldLayout.key];

        if(!field) {
            return config('app.debug');
        }

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

    fieldHasError(field: Omit<FormFieldData, 'value'>, key: string, locale = null): boolean {
        if(this.fieldError(key)) {
            return true;
        }
        if('localized' in field && field.localized) {
            return locale
                ? this.fieldLocalesContainingError(key).includes(locale)
                : this.fieldLocalesContainingError(key).length > 0;
        }
        return false;
    }

    fieldLocalesContainingError(key: string): string[] {
        return this.locales.filter(locale => this.fieldError(`${key}.${locale}`));
    }

    fieldIsEmpty(field: Omit<FormFieldData, 'value'>, value: FormFieldData['value'], locale: string): boolean {
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

    listFieldErrorCount(field: FormListFieldData, key: string): number {
        return (this.data[key] as FormListFieldData['value'])
            ?.flatMap((item, index) =>
                Object.keys(field.itemFields).filter(fieldKey => this.fieldHasError(field, `${key}.${index}.${fieldKey}`))
            )
            .length ?? 0;
    }

    tabErrorCount(tab: FormLayoutTabData): number {
        const tabFields = tab.columns
            .map(col =>
                col.fields.flat(2).map(fieldLayout =>
                    'legend' in fieldLayout ? fieldLayout.fields.flat(2) : fieldLayout
                )
            )
            .flat(2)
            .map(fieldLayout => this.fields[fieldLayout.key])
            .filter(Boolean);

        return tabFields.reduce((count, field) => {
            if(field.type === 'list') {
                return count + this.listFieldErrorCount(field, field.key);
            }
            if(this.fieldHasError(field, field.key, null)) {
                return count + 1;
            }
            return count;
        }, 0);
    }

    eventTarget: EventTarget = new EventTarget();

    addEventListener(type: FormEvents, callback: EventListener, options?: AddEventListenerOptions | boolean): void {
        this.eventTarget.addEventListener(type, callback, options);
    }

    dispatchEvent(event: Event): boolean {
        return this.eventTarget.dispatchEvent(event);
    }

    removeEventListener(type: FormEvents, callback: EventListener, options?: EventListenerOptions | boolean): void {
        console.log('removeEventListener', type, callback, options);
        this.eventTarget.removeEventListener(type, callback, options);
    }
}
