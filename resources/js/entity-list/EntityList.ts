import {
    CommandData,
    ConfigCommandsData,
    EntityListData,
    EntityListFieldData,
    EntityStateValueData,
    FilterData,
} from "@/types";
import { getAppendableParentUri, route } from "@/utils/url";
import { EntityListInstance, InstanceId } from "./types";

export class EntityList implements EntityListData {
    authorizations: EntityListData['authorizations'];
    config: EntityListData['config'];
    data: EntityListData['data'];
    fields: EntityListData['fields'];
    forms: EntityListData['forms'];
    meta: EntityListData['meta'];
    pageAlert: EntityListData['pageAlert'];
    query: EntityListData['query'];
    filterValues: EntityListData['filterValues'];
    title: EntityListData['title'];

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
        return this.meta?.total ?? this.data.length;
    }

    get currentSort() {
        return this.query?.sort ?? this.config.defaultSort;
    }

    get currentSortDir() {
        return this.query?.dir ?? this.config.defaultSortDir;
    }

    get visibleFilters(): Array<FilterData>|null {
        return this.hiddenFilters
            ? this.config.filters?._root.filter(filter => !(filter.key in this.hiddenFilters))
            : this.config.filters?._root;
    }

    get visibleCommands(): ConfigCommandsData {
        return {
            instance: this.config.commands?.instance?.map(group => group.filter(command => {
                return !this.hiddenCommands?.instance?.includes(command.key);
            })),
            entity: this.config.commands?.entity?.map(group => group.filter(command => {
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
        return this.allowedEntityCommands.flat().some(command => command.instanceSelection);
    }

    get canReorder() {
        return this.config.reorderable
            && this.authorizations.reorder
            && this.data.length > 1;
    }

    withRefreshedItems(refreshedItems: EntityListInstance[]): EntityList {
        this.data = this.data.map(item => {
            return refreshedItems.find(refreshedItem => this.instanceId(refreshedItem) === this.instanceId(item))
                ?? item;
        });
        return new EntityList(this, this.entityKey, this.hiddenFilters, this.hiddenCommands);
    }

    dropdownEntityCommands(selecting: boolean) {
        return this.allowedEntityCommands.map(commandGroup =>
            commandGroup.filter(command => {
                if(selecting) {
                    return !!command.instanceSelection;
                }
                return !command.primary;
            })
        );
    }

    instanceId(instance: EntityListInstance): InstanceId {
        return instance[this.config.instanceIdAttribute];
    }

    instanceUrl(instance: EntityListInstance): string | null {
        const entityKey = this.entityKey;
        const instanceId = this.instanceId(instance);

        if(!this.authorizations.view.includes(instanceId)) {
            return null;
        }

        const multiform = this.forms && Object.values(this.forms).find(form => form.instances.includes(instanceId));

        if(this.config.hasShowPage) {
            return route('code16.sharp.show.show', {
                parentUri: getAppendableParentUri(),
                entityKey: multiform ? `${entityKey}:${multiform.key}` : entityKey,
                instanceId,
            });
        }

        return route('code16.sharp.form.edit', {
            parentUri: getAppendableParentUri(),
            entityKey: multiform ? `${entityKey}:${multiform.key}` : entityKey,
            instanceId,
        });
    }

    instanceState(instance: EntityListInstance): string | number | null {
        return this.config.state
            ? instance[this.config.state.attribute]
            : null;
    }

    instanceStateValue(instance: EntityListInstance): EntityStateValueData | undefined {
        return this.config.state?.values.find(item => item.value === this.instanceState(instance));
    }

    instanceCanUpdateState(instance: EntityListInstance): boolean {
        if(Array.isArray(this.config.state.authorization)) {
            return this.config.state.authorization.includes(this.instanceId(instance));
        }
        return !!this.config.state.authorization;
    }

    instanceCanDelete(instance: EntityListInstance): boolean {
        if(Array.isArray(this.authorizations.delete)) {
            return this.authorizations.delete?.includes(this.instanceId(instance));
        }
        return !!this.authorizations.delete;
    }

    instanceCommands(instance: EntityListInstance): Array<Array<CommandData>> | undefined {
        return this.visibleCommands?.instance
            ?.map(group => group.filter(command => {
                if(Array.isArray(command.authorization)) {
                    return command.authorization.includes(this.instanceId(instance));
                }
                return command.authorization;
            }));
    }

    instanceHasActions(instance: EntityListInstance, showEntityState: boolean): boolean {
        return (
            this.instanceCommands(instance)?.flat().length > 0 ||
            this.config.state && showEntityState && this.instanceCanUpdateState(instance) ||
            !this.config.deleteHidden && this.instanceCanDelete(instance)
        );
    }

    fieldShouldBeVisible(field: EntityListFieldData, showEntityState: boolean): boolean {
        if(field.type === 'badge') {
            return this.data.some(item =>
                item[field.key] === true
                || typeof item[field.key] === 'number'
                || typeof item[field.key] === 'string' && item[field.key].length > 0
            );
        }

        if(field.type === 'state') {
            return this.config.state && showEntityState;
        }

        return true;
    }
}
