<script setup lang="ts">
    import ActionBar from "./ActionBar.vue";
    import FieldLayout from "../../FieldLayout.vue";
    import { EntityList, EntityListTitle } from '@sharp/entity-list';
    import { FieldProps } from "../../types";
    import { ShowEntityListFieldData } from "@/types";
    import { nextTick, onMounted, onUnmounted, ref } from "vue";
    import { getNavbarHeight } from "@sharp/ui";

    const props = defineProps<FieldProps & {
        field: ShowEntityListFieldData,
        value: ShowEntityListFieldData['value'],
    }>();

    const el = ref();
    const collapsed = ref(props.collapsable);
    const sticky = ref(false);
    const layout = () => {
        sticky.value = el.value.offsetHeight > (window.innerHeight - getNavbarHeight());
    }

    async function onListChanged() {
        await nextTick();
        layout();
    }

    onMounted(() => {
        window.addEventListener('resize', layout);
    });

    onUnmounted(() => {
        window.removeEventListener('resize', layout);
    });
</script>

<template>
    <div ref="el">
       <FieldLayout
           class="ShowEntityListField"
           :class="{
               'ShowEntityListField--collapsed': collapsed,
           }"
       >
           <EntityList
               v-if="value"
               :entity-list="value"
               :entity-key="field.entityListKey"
               :module="storeModule"
               :show-create-button="field.showCreateButton"
               :show-reorder-button="field.showReorderButton"
               :show-search-field="field.showSearchField"
               :show-entity-state="field.showEntityState"
               :hidden-commands="field.hiddenCommands"
               :filters="visibleFilters"
               :visible="!collapsed"
               inline
               @change="onListChanged"
               @reordering="$emit('reordering', $event)"
           >
               <template v-slot:title="{ count }">
                   <template v-if="collapsable">
                       <div class="section__header section__header--collapsable position-relative">
                           <div class="row align-items-center gx-0 h-100">
                               <div class="col-auto">
                                   <details :open="!collapsed" @toggle="collapsed = !$event.target.open">
                                       <summary class="stretched-link">
                                           <span class="visually-hidden">{{ field.label }}</span>
                                       </summary>
                                   </details>
                               </div>
                               <div class="col">
                                   <EntityListTitle :count="field.showCount ? count : null">
                                       <h2 class="ShowEntityListField__label section__title mb-0">
                                           {{ field.label }}
                                       </h2>
                                   </EntityListTitle>
                               </div>
                           </div>
                       </div>
                   </template>
                   <template v-else>
                       <div class="section__header d-grid">
                           <EntityListTitle :count="field.showCount ? count : null">
                               <h2 class="ShowEntityListField__label section__title mb-0">
                                   {{ field.label }}
                               </h2>
                           </EntityListTitle>
                       </div>
                   </template>
               </template>
<!--               <template v-slot:action-bar="{ props, listeners }">-->
<!--                   <ActionBar-->
<!--                       class="ShowEntityListField__action-bar"-->
<!--                       v-bind="props"-->
<!--                       v-on="listeners"-->
<!--                       :collapsed="collapsed"-->
<!--                       :sticky="sticky"-->
<!--                   >-->
<!--                      -->
<!--                   </ActionBar>-->
<!--               </template>-->
           </EntityList>
       </FieldLayout>
   </div>
</template>

<script lang="ts">
    import { entityListModule } from '@sharp/entity-list';

    // TODO
    export default {
        computed: {
            storeModule() {
                return `show/entity-lists/${this.field.entityListKey}`;
            },
            query() {
                return this.storeGetter('query');
            },
            filters() {
                return this.storeGetter('filters/rootFilters');
            },
            getFiltersQueryParams() {
                return this.storeGetter('filters/getQueryParams');
            },
            filtersValues() {
                return this.storeGetter('filters/values');
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
        },
        methods: {
            storeGetter(name) {
                return this.$store.getters[`${this.storeModule}/${name}`];
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
        },
    }
</script>
