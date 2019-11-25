<template>
    <SharpEntityList
        class="ShowEntityListField"
        :entity-key="entityListKey"
        :module="storeModule"
        :show-create-button="showCreateButton"
        :show-reorder-button="showReorderButton"
        :show-search-field="showSearchField"
        :hidden-commands="hiddenCommands"
        inline
    >
        <template slot="action-bar" slot-scope="{ props, listeners }">
            <div class="mb-2">
                <ActionBar v-bind="props" v-on="listeners" />
            </div>
        </template>
        <template slot="append-head" slot-scope="{ props: { commands }, listeners }">
            <template v-if="hasCommands(commands)">
                <SharpCommandsDropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--commands"
                    :commands="commands"
                    @select="listeners['command']"
                >
                    <div slot="text">{{ l('entity_list.commands.entity.label') }}</div>
                </SharpCommandsDropdown>
            </template>
        </template>
    </SharpEntityList>
</template>

<script>
    import EntityListModule from '../../../../store/modules/entity-list';
    import SharpEntityList from "../../../list/EntityList";
    import ActionBar from "./ActionBar";
    import SharpCommandsDropdown from "../../../commands/CommandsDropdown";
    import { getFiltersQueryParams } from "../../../../util/filters";
    import { Localization } from "../../../../mixins";

    export default {
        mixins: [Localization],
        components: {
            SharpEntityList,
            SharpCommandsDropdown,
            ActionBar,
        },
        props: {
            entityListKey: String,
            showCreateButton: Boolean,
            showReorderButton: Boolean,
            showSearchField: Boolean,
            hiddenFilters: Object,
            hiddenCommands: Object,
        },
        computed: {
            storeModule() {
                return `show/entity-lists/${this.entityListKey}`;
            },
        },
        methods: {
            hasCommands(commands) {
                return commands && commands.some(group => group && group.length > 0);
            },
        },
        created() {
            this.$store.registerModule(this.storeModule.split('/'), EntityListModule);
            this.$store.dispatch(`${this.storeModule}/setQuery`, getFiltersQueryParams(this.hiddenFilters));
        },
    }
</script>