import {
    CommandData,
    EntityStateValueData, LayoutFieldData,
    ShowData,
    ShowFieldData,
    ShowFieldType, ShowLayoutColumnData,
    ShowLayoutSectionData,
    ShowListFieldData,
    ShowTextFieldData
} from "@/types";
import { getAppendableParentUri, route } from "@/utils/url";
import { config } from "@/utils/config";


export class Show implements ShowData {
    authorizations: ShowData['authorizations'];
    config: ShowData['config'];
    data: ShowData['data'];
    fields: ShowData['fields'];
    layout: ShowData['layout'];
    locales: ShowData['locales'];
    pageAlert: ShowData['pageAlert'];
    title: ShowData['title'];

    entityKey: string;
    instanceId?: string;

    constructor(data: ShowData, entityKey: string, instanceId?: string) {
        Object.assign(this, data);
        this.entityKey = entityKey;
        this.instanceId = instanceId;
    }

    get formUrl(): string {
        if(route().params.instanceId) {
            return route('code16.sharp.form.edit', {
                parentUri: getAppendableParentUri(),
                entityKey: this.entityKey,
                instanceId: this.instanceId,
            });
        }

        return route('code16.sharp.form.create', {
            parentUri: getAppendableParentUri(),
            entityKey: this.entityKey,
        });
    }

    get instanceState(): string | number | null {
        return this.config.state
            ? this.data[this.config.state.attribute] as any
            : null;
    }

    get instanceStateValue(): EntityStateValueData | undefined {
        return this.config.state?.values.find(item => item.value === this.instanceState);
    }

    get allowedInstanceCommands(): Array<Array<CommandData>> | undefined {
        return this.config.commands?.instance
            ?.map(group => group.filter(command => command.authorization));
    }

    getTitle(locale: string): string | null {
        if(this.title) {
            return typeof this.title === 'object'
                ? this.title?.[locale]
                : this.title;
        }
        return null;
    }

    sectionFields(section: ShowLayoutSectionData): Array<ShowFieldData> {
        return section.columns
            .map((column) => column.fields.flat().map(field => this.fields[field.key]))
            .flat()
            .filter(Boolean);
    }

    sectionHasField(section: ShowLayoutSectionData, type: ShowFieldType): boolean {
        return this.sectionFields(section).some(field => field.type === type);
    }

    sectionCommands(section: ShowLayoutSectionData): Array<Array<CommandData>> | null {
        if(!section.key) {
            return null;
        }
        return (this.config.commands[section.key] ?? [])
            .map(group => group.filter(command => command.authorization));
    }

    sectionShouldBeVisible(section: ShowLayoutSectionData, locale: string): boolean {
        return section.columns
            .map((column) => column.fields)
            .flat(2)
            .some(fieldLayout => this.fieldShouldBeVisible(fieldLayout, locale));
    }

    fieldRowShouldBeVisible(row: ShowLayoutColumnData['fields'][0], locale: string, fields = this.fields, data = this.data): boolean {
        return row
            .some(fieldLayout => this.fieldShouldBeVisible(fieldLayout, locale, fields, data));
    }

    fieldShouldBeVisible(fieldLayout: LayoutFieldData, locale: string, fields = this.fields, data = this.data): boolean {
        const field = fields[fieldLayout.key];

        if(!field) {
            return config('app.debug');
        }

        if(field.type === 'entityList') {
            return true;
        }

        if(field.emptyVisible) {
            return true;
        }

        if(field.type === 'text') {
            const value = data[field.key] as ShowTextFieldData['value'];
            return field.localized
                ? !!value?.text?.[locale]
                : !!value?.text;
        }

        if(field.type === 'list') {
            const value = data[field.key] as ShowListFieldData['value']
            return value?.length > 0;
        }

        return !!data[field.key];
    }
}
