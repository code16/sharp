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
            @reordering="$emit('reordering', $event)"
        >
            <template v-slot:action-bar="{ props, listeners }">
                <ActionBar
                    class="ShowEntityListField__action-bar"
                    v-bind="props"
                    v-on="listeners"
                    :collapsed="collapsed"
                    :filters="visibleFilters"
                    :has-active-query="hasActiveQuery"
                    :sticky="sticky"
                >
                    <template v-if="hasCollapse">
                        <div class="section__header section__header--collapsable position-relative">
                            <div class="row align-items-center gx-0 h-100">
                                <div class="col-auto">
                                    <details :open="!collapsed" @toggle="handleDetailsToggle">
                                        <summary class="stretched-link">
                                            <span class="visually-hidden">{{ label }}</span>
                                        </summary>
                                    </details>
                                </div>
                                <div class="col">
                                    <EntityListTitle :count="showCount ? props.count : null">
                                        <h2 class="ShowEntityListField__label section__title mb-0">
                                            {{ label }}
                                        </h2>
                                    </EntityListTitle>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="section__header d-grid">
                            <EntityListTitle :count="showCount ? props.count : null">
                                <h2 class="ShowEntityListField__label section__title mb-0">
                                    {{ label }}
                                </h2>
                            </EntityListTitle>
                        </div>
                    </template>
                </ActionBar>
            </template>
        </EntityList>
    </FieldLayout>
</template>

<script>
    import { entitiesMatch } from "sharp";
    import { getNavbarHeight } from "sharp-ui";
    import { getReferrerRoute } from "sharp/router";
    import { Localization } from "sharp/mixins";
    import { EntityList, EntityListTitle, entityListModule } from 'sharp-entity-list';
    import { CommandsDropdown } from 'sharp-commands';
    import { sticky } from "sharp/directives";

    import ActionBar from "./ActionBar";
    import FieldLayout from "../../FieldLayout";
    import { syncVisibility } from "../../../util/fields/visiblity";

    export default {
        mixins: [Localization],
        components: {
            EntityListTitle,
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
            showCount: Boolean,
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
                sticky: false,
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
                return this.storeGetter('filters/pageFilters');
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
                        data.list.items?.length > 0 ||
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
            async handleChanged(list) {
                this.list = list;
                await this.$nextTick();
                this.layout();
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
            layout() {
                this.sticky = this.$el.offsetHeight > (window.innerHeight - getNavbarHeight());
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

            window.addEventListener('resize', () => this.layout());
        },
    }
</script>
