<template>
    <EntityList
        class="ShowEntityListField"
        :entity-key="entityListKey"
        :module="storeModule"
        :show-create-button="showCreateButton"
        :show-reorder-button="showReorderButton"
        :show-search-field="showSearchField"
        :show-entity-state="showEntityState"
        :hidden-commands="hiddenCommands"
        :hidden-filters="hiddenFilters"
        @change="handleChanged"
        inline
    >
        <template slot="action-bar" slot-scope="{ props, listeners }">
            <ActionBar class="ShowEntityListField__action-bar" v-bind="props" v-on="listeners" />
        </template>
        <template slot="append-head" slot-scope="{ props: { commands }, listeners }">
            <template v-if="hasCommands(commands)">
                <CommandsDropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--commands"
                    :commands="commands"
                    @select="listeners['command']"
                >
                    <div slot="text">{{ l('entity_list.commands.entity.label') }}</div>
                </CommandsDropdown>
            </template>
        </template>
    </EntityList>
</template>

<script>
    import { EntityList, entityListModule } from 'sharp-entity-list';
    import { CommandsDropdown } from 'sharp-commands';
    import { Localization } from "sharp/mixins";

    import ActionBar from "./ActionBar";
    import {syncVisibility} from "../../../util/fields/visiblity";

    export default {
        mixins: [Localization],
        components: {
            EntityList,
            CommandsDropdown,
            ActionBar,
        },
        props: {
            entityListKey: String,
            showCreateButton: Boolean,
            showReorderButton: Boolean,
            showSearchField: Boolean,
            showEntityState: Boolean,
            hiddenFilters: Object,
            hiddenCommands: Object,
        },
        data() {
            return {
                list: null,
            }
        },
        computed: {
            storeModule() {
                return `show/entity-lists/${this.entityListKey}`;
            },
            getFiltersQueryParams() {
                return this.storeGetter('filters/getQueryParams');
            },
            isVisible() {
                if(this.list) {
                    const { data, authorizations } = this.list;
                    return !!(
                        data.items && data.items.length > 0 ||
                        this.showCreateButton && authorizations.create ||
                        this.emptyVisible
                    );
                }
                return this.emptyVisible;
            },
        },
        methods: {
            hasCommands(commands) {
                return commands && commands.some(group => group && group.length > 0);
            },
            storeGetter(name) {
                return this.$store.getters[`${this.storeModule}/${name}`];
            },
            handleChanged(list) {
                this.list = list;
            },
        },
        created() {
            this.$store.registerModule(this.storeModule.split('/'), entityListModule);
            this.$store.dispatch(`${this.storeModule}/setQuery`, this.getFiltersQueryParams(this.hiddenFilters));

            syncVisibility(this, () => this.isVisible, { lazy:true });
        },
    }
</script>