<template>
    <FieldLayout class="ShowEntityListField" :class="classes">
        <EntityList
            :entity-key="entityListKey"
            :module="storeModule"
            :show-create-button="showCreateButton"
            :show-reorder-button="showReorderButton"
            :show-search-field="showSearchField"
            :show-entity-state="showEntityState"
            :hidden-commands="hiddenCommands"
            :visible="!collapsed"
            :focused-item="focusedItem"
            inline
            @change="handleChanged"
        >
            <template v-slot:action-bar="{ props, listeners }">
                <ActionBar class="ShowEntityListField__action-bar"
                    v-bind="props"
                    v-on="listeners"
                    :collapsed="collapsed"
                    :filters="visibleFilters"
                    :has-active-query="hasActiveQuery"
                >
                    <template v-if="hasCollapse">
                        <details :open="!collapsed" @toggle="handleDetailsToggle">
                            <summary class="py-1">
                                <h2 class="ShowEntityListField__label show-field__label d-inline-block">
                                    {{ label || ' ' }}
                                </h2>
                            </summary>
                        </details>
                    </template>
                    <template v-else>
                        <div class="ShowEntityListField__label show-field__label">
                            {{ label }}
                        </div>
                    </template>
                </ActionBar>
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
    </FieldLayout>
</template>

<script>
    import { entitiesMatch } from "sharp";
    import { getReferrerRoute } from "sharp/router";
    import { Localization } from "sharp/mixins";
    import { EntityList, entityListModule } from 'sharp-entity-list';
    import { CommandsDropdown } from 'sharp-commands';

    import ActionBar from "./ActionBar";
    import FieldLayout from "../../FieldLayout";
    import { syncVisibility } from "../../../util/fields/visiblity";


    export default {
        mixins: [Localization],
        components: {
            EntityList,
            CommandsDropdown,
            ActionBar,
            FieldLayout,
        },
        props: {
            fieldKey: String,
            entityListKey: String,
            showCreateButton: Boolean,
            showReorderButton: Boolean,
            showSearchField: Boolean,
            showEntityState: Boolean,
            hiddenFilters: Object,
            hiddenCommands: Object,
            label: String,
            emptyVisible: Boolean,
            collapsable: Boolean,
        },
        data() {
            return {
                list: null,
                collapsed: this.collapsable && !this.getFocusedItem(),
                focusedItem: this.getFocusedItem(),
            }
        },
        computed: {
            classes() {
                return {
                    'ShowEntityListField--collapsed': this.collapsed,
                }
            },
            storeModule() {
                return `show/entity-lists/${this.fieldKey}`;
            },
            query() {
                return this.storeGetter('query');
            },
            filters() {
                return this.storeGetter('filters/filters');
            },
            getFiltersQueryParams() {
                return this.storeGetter('filters/getQueryParams');
            },
            filtersValues() {
                return this.storeGetter('filters/values');
            },
            isVisible() {
                if(this.hasCollapse || this.emptyVisible) {
                    return true;
                }
                if(this.list) {
                    const { data, authorizations } = this.list;
                    return !!(
                        data.items && data.items.length > 0 ||
                        this.showCreateButton && authorizations.create ||
                        this.hasActiveQuery
                    );
                }
            },
            visibleFilters() {
                return this.hiddenFilters
                    ? this.filters.filter(filter => !(filter.key in this.hiddenFilters))
                    : this.filters;
            },
            hasActiveQuery() {
                const hasActiveFilters = this.visibleFilters
                    .some(filter => this.filtersValues[filter.key] != null);

                return !!this.query.search || hasActiveFilters;
            },
            hasCollapse() {
                return this.collapsable;
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
            handleDetailsToggle(e) {
                this.collapsed = !e.target.open;
            },
            getFocusedItem() {
                const route = getReferrerRoute();
                if(route?.name
                    && entitiesMatch(route.params.entityKey, this.entityListKey)
                    && route.params.instanceId
                    && route.path.length > this.$route.path.length
                ) {
                    return Number(route.params.instanceId);
                }
            },
        },
        created() {
            const modulePath = this.storeModule.split('/');
            if(!this.$store.hasModule(modulePath)) {
                this.$store.registerModule(modulePath, entityListModule);
            }
            if(this.hiddenFilters) {
                this.$store.dispatch(`${this.storeModule}/setQuery`, this.getFiltersQueryParams(this.hiddenFilters));
            }

            syncVisibility(this, () => this.isVisible, { lazy:true });
        },
        mounted() {
            if(this.focusedItem) {
                const rect = this.$el.getBoundingClientRect();
                window.scrollBy(0, rect.top - 100);
            }
        }
    }
</script>
