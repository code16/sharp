import {
    CommandData,
    ConfigCommandsData,
    EntityAuthorizationsData,
    EntityListConfigData,
    EntityListData, EntityListDataData,
    EntityListFieldData, EntityListFieldLayoutData, EntityListMultiformData, FilterData,
    ShowHtmlFieldData
} from "@/types";
import { getAppendableUri, route } from "@/utils/url";
import { Instance, InstanceId } from "./types";

export class EntityList implements EntityListData {
    authorizations: EntityAuthorizationsData;
    config: EntityListConfigData;
    containers: { [p: string]: EntityListFieldData };
    data: EntityListDataData;
    fields: { [p: string]: any };
    forms: { [p: string]: EntityListMultiformData };
    layout: Array<EntityListFieldLayoutData>;

    entityKey: string;
    hiddenFilters?: Record<string, FilterData['value']>;
    hiddenCommands: { entity: string[], instance: string[] };

    constructor(
        data: EntityListData,
        entityKey: string,
        hiddenFilters?: Record<string, FilterData['value']>,
        hiddenCommands?: { entity: string[], instance: string[] }
    ) {
        Object.assign(this, data);

        this.entityKey = entityKey;
        this.hiddenFilters = hiddenFilters;
        this.hiddenCommands = hiddenCommands;
    }

    get count() {
        return this.data.list.totalCount ?? this.data.list.items.length;
    }

    get visibleFilters(): Array<FilterData> {
        return this.config.filters._root.filter(filter => !this.hiddenFilters?.[filter.key]);
    }

    get visibleCommands(): ConfigCommandsData {
        return {
            instance: this.config.commands.instance?.map(group => group.filter(command => {
                return !this.hiddenCommands?.instance?.includes(command.key);
            })),
            entity: this.config.commands.entity?.map(group => group.filter(command => {
                return !this.hiddenCommands?.entity?.includes(command.key);
            })),
        }
    }

    get allowedEntityCommands() {
        return this.visibleCommands.entity
            ?.map(group => group.filter(command => command.authorization))
            ?? [];
    }

    get primaryCommand(): CommandData {
        return this.allowedEntityCommands.flat().find(command => command.primary);
    }

    get canSelect() {
        return this.allowedEntityCommands.flat().some(command => command.instance_selection);
    }

    get canReorder() {
        return this.config.reorderable
            && this.authorizations.update
            && this.data.list.items.length > 0;
    }

    withRefreshedItems(refreshedItems: Instance[]): EntityList {
        this.data.list.items = this.data.list.items.map(item => {
            return refreshedItems.find(refreshedItem => this.instanceId(refreshedItem) === this.instanceId(item))
                ?? item;
        });
        return new EntityList(this, this.entityKey);
    }

    dropdownEntityCommands(selecting: boolean) {
        return this.allowedEntityCommands.map(commandGroup =>
            commandGroup.filter(command => {
                if(selecting) {
                    return !!command.instance_selection;
                }
                return !command.primary;
            })
        );
    }

    instanceId(instance: Instance): InstanceId {
        return instance[this.config.instanceIdAttribute];
    }

    instanceUrl(instance: Instance) {
        const entityKey = this.entityKey;
        const instanceId = this.instanceId(instance);

        if(!this.authorizations.view.includes(instanceId)) {
            return null;
        }

        if(this.config.hasShowPage) {
            return route('code16.sharp.show.show', {
                uri: getAppendableUri(),
                entityKey,
                instanceId,
            });
        }

        if(this.forms) {
            const multiform = Object.values(this.forms).find(form => form.instances.includes(instanceId));

            return route('code16.sharp.form.edit', {
                uri: getAppendableUri(),
                entityKey: `${entityKey}:${multiform.key}`,
                instanceId,
            });
        }

        return route('code16.sharp.form.edit', {
            uri: getAppendableUri(),
            entityKey,
            instanceId,
        });
    }

    createUrl(multiform?: EntityListMultiformData) {
        const entityKey = this.entityKey;

        if(multiform) {
            return route('code16.sharp.form.create', {
                uri: getAppendableUri(),
                entityKey: `${entityKey}:${multiform.key}`,
            });
        }

        return route('code16.sharp.form.create', {
            uri: getAppendableUri(),
            entityKey,
        });
    }
}
