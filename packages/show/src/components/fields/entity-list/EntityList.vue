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
        inline
    >
        <template v-slot:action-bar="{ props, listeners }">
            <ActionBar class="ShowEntityListField__action-bar" v-bind="props" v-on="listeners" />
        </template>
        <template v-slot:append-head="{ props: { commands }, listeners }">
            <template v-if="hasCommands(commands)">
                <CommandsDropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--commands"
                    :commands="commands"
                    @select="listeners['command']"
                >
                    <template v-slot:text>
                        {{ l('entity_list.commands.entity.label') }}
                    </template>
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
        computed: {
            storeModule() {
                return `show/entity-lists/${this.entityListKey}`;
            },
            getFiltersQueryParams() {
                return this.storeGetter('filters/getQueryParams');
            },
        },
        methods: {
            hasCommands(commands) {
                return commands && commands.some(group => group && group.length > 0);
            },
            storeGetter(name) {
                return this.$store.getters[`${this.storeModule}/${name}`];
            },
        },
        created() {
            this.$store.registerModule(this.storeModule.split('/'), entityListModule);
            this.$store.dispatch(`${this.storeModule}/setQuery`, this.getFiltersQueryParams(this.hiddenFilters));
        },
    }
</script>