import {
    CommandData, EntityStateData, EntityStateValueData,
    InstanceAuthorizationsData,
    ShowConfigData,
    ShowData,
    ShowFieldData,
    ShowLayoutData, ShowTextFieldData
} from "@/types";
import { getAppendableUri, route } from "@/utils/url";


export class Show implements ShowData {
    authorizations: InstanceAuthorizationsData;
    config: ShowConfigData;
    data: { [p: string]: any };
    fields: { [p: string]: ShowFieldData };
    layout: ShowLayoutData;
    locales: Array<string> | null;

    constructor(data: ShowData) {
        Object.assign(this, data);
    }

    get formUrl(): string {
        const formKey = this.config.multiformAttribute
            ? this.data[this.config.multiformAttribute]
            : null;
        const entityKey = formKey
            ? `${route().params.entityKey}:${formKey}`
            : route().params.entityKey;

        if(route().params.instanceId) {
            return route('code16.sharp.form.edit', {
                uri: getAppendableUri(),
                entityKey,
                instanceId: route().params.instanceId,
            });
        }

        return route('code16.sharp.form.create', {
            uri: getAppendableUri(),
            entityKey,
        });
    }

    get instanceState(): string | number | null {
        return this.config.state
            ? this.data[this.config.state.attribute]
            : null;
    }

    get instanceStateValue(): EntityStateValueData | undefined {
        return this.config.state?.values.find(item => item.value === this.instanceState);
    }

    get authorizedCommands(): Array<Array<CommandData>> | undefined {
        return this.config.commands?.instance
            ?.map(group => group.filter(command => command.authorization));
    }

    get canDelete(): boolean {
        return this.authorizations.delete && !this.config.isSingle;
    }

    title(locale: string): string {
        if(!this.config.titleAttribute) {
            return null;
        }
        if((this.fields[this.config.titleAttribute] as ShowTextFieldData).localized) {
            return this.data[this.config.titleAttribute]?.[locale];
        }
        return this.data[this.config.titleAttribute];
    }
}
