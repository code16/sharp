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

    get instanceState(): string | number | null {
        return this.config.state
            ? this.data[this.config.state.attribute] as any
            : null;
    }

    get instanceStateValue(): EntityStateValueData | undefined {
        return this.config.state?.values.find(item => item.value === this.instanceState);
    }

    get allowedInstanceCommands(): CommandData[][] {
        return this.config.commands?.instance
            ?.map(group => group.filter(command => command.authorization));
    }

    get dropdownInstanceCommands(): CommandData[][] {
        return this.allowedInstanceCommands
            ?.map(group => group.filter(command => !command.primary));
    }

    get primaryInstanceCommands(): CommandData[] | undefined {
        return this.allowedInstanceCommands
            ?.flat()
            .filter(command => command.primary);
    }

    sectionAllowedCommands(section: ShowLayoutSectionData): CommandData[][] | null {
        if(!section.key) {
            return null;
        }
        return (this.config.commands?.[section.key] ?? [])
            .map(group => group.filter(command => command.authorization));
    }

    sectionDropdownCommands(section: ShowLayoutSectionData): CommandData[][] | null {
        if(!section.key) {
            return null;
        }
        return this.sectionAllowedCommands(section).map(group => group.filter(command => !command.primary));
    }

    sectionPrimaryCommands(section: ShowLayoutSectionData): CommandData[] | null {
        if(!section.key) {
            return null;
        }
        return this.sectionAllowedCommands(section).flat().filter(command => command.primary);
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

        if(field.type === 'entityList' || field.type === 'dashboard') {
            return field.authorizations.view;
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
