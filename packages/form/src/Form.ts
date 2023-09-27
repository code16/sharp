import {
    FormData,
    FormEditorFieldData,
    FormFieldData,
    FormLayoutFieldsetData,
    FormLayoutTabData,
    FormListFieldData
} from "@/types";
import { computeCondition } from "./util/conditional-display";
import { reactive } from "vue";

export class Form  implements FormData {
    authorizations: FormData['authorizations'];
    config: FormData['config'];
    fields: FormData['fields'];
    layout: FormData['layout'];
    locales: FormData['locales'];
    pageAlert: FormData['pageAlert'];

    state = reactive<{ errors: { [key: string]: string[] }, data: FormData['data'] }>({
        errors: {},
        data: {},
    });

    constructor(data: FormData) {
        Object.assign(this, data);
    }

    set data(data) {
        this.state.data = data;
    }

    get data() {
        return this.state.data;
    }

    get errors() {
        return this.state.errors;
    }

    set errors(errors) {
        this.state.errors = errors;
    }

    setError(key: string, error: string) {
        this.errors[key] = [error];
    }

    clearError(key: string) {
        delete this.errors[key];
    }

    fieldShouldBeVisible(field: FormFieldData, fields = this.fields, data = this.data) {
        if(!field.conditionalDisplay) {
            return true;
        }

        return computeCondition(fields, data, field.conditionalDisplay);
    }

    fieldsetShouldBeVisible(fieldset: FormLayoutFieldsetData, fields = this.fields, data = this.data) {
        return fieldset.fields
            .flat(2)
            .some(fieldLayout => this.fieldShouldBeVisible(fields[fieldLayout.key], fields, data));
    }

    fieldError(key: string): string | undefined {
        return this.errors[key]?.[0];
    }

    fieldHasError(field: FormFieldData, key: string, includeChildren = false): boolean {
        if(this.fieldError(key)) {
            return true;
        }
        if('localized' in field && field.localized) {
            return this.fieldLocalesContainingError(key).length > 0;
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
                return !!(value as FormEditorFieldData['value']).text[locale];
            }
            if(field.type === 'text' || field.type === 'textarea') {
                return !!value[locale];
            }
        }
        return !!value;
    }

    tabHasError(tab: FormLayoutTabData): boolean {
        const tabFields = tab.columns
            .map(col =>
                col.fields.flat(2).map(fieldLayout =>
                    'legend' in fieldLayout ? fieldLayout.fields.flat(2) : fieldLayout
                )
            )
            .flat(2)
            .map(fieldLayout => this.fields[fieldLayout.key])
            .filter(Boolean);

        return tabFields.some(field => this.fieldHasError(field, field.key, true));
    }
}
