<template>
    <div class="action-bar">
        <template v-if="hasLeftControls && $slots.default">
            <div class="mb-2">
                <slot />
            </div>
        </template>
        <div class="row action-bar__row">
            <template v-if="hasLeftControls">
                <div class="col-sm action-bar__col">
                    <template v-if="!reorderActive">
                        <div class="row action-bar__row">
                            <template v-for="filter in filters">
                                <div class="col-auto action-bar__col mb-2">
                                    <div class="action-bar__element">
                                        <FilterDropdown
                                            class="h-100"
                                            :filter="filter"
                                            :value="filtersValues[filter.key]"
                                            @input="handleFilterChanged(filter, $event)"
                                            :key="filter.id"
                                        />
                                    </div>
                                </div>
                            </template>
                            <template v-if="canSearch">
                                <div class="col-auto action-bar__col mb-2">
                                    <div class="action-bar__element">
                                        <Search
                                            class="h-100"
                                            :value="search"
                                            :active.sync="searchActive"
                                            :placeholder="l('action_bar.list.search.placeholder')"
                                            @input="handleSearchInput"
                                            @submit="handleSearchSubmitted"
                                        />
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
            <template v-else>
                <div class="col-sm align-self-center action-bar__col mb-2">
                    <slot />
                </div>
            </template>

            <div class="col-sm-auto action-bar__col">
                <div class="row flex-nowrap justify-content-end action-bar__row">
                    <template v-if="canReorder">
                        <div class="col-auto action-bar__col mb-2">
                            <template v-if="reorderActive">
                                <div class="row action-bar__row">
                                    <div class="col-auto action-bar__col">
                                        <button class="SharpButton SharpButton--secondary-accent" @click="handleReorderButtonClicked">
                                            {{ l('action_bar.list.reorder_button.cancel') }}
                                        </button>
                                    </div>
                                    <div class="col-auto action-bar__col">
                                        <button class="SharpButton SharpButton--accent" @click="handleReorderSubmitButtonClicked">
                                            {{ l('action_bar.list.reorder_button.finish') }}
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <button class="SharpButton SharpButton--secondary-accent" @click="handleReorderButtonClicked">
                                    {{ l('action_bar.list.reorder_button') }}
                                </button>
                            </template>
                        </div>
                    </template>
                    <template v-if="canCreate && !reorderActive">
                        <div class="col-auto action-bar__col mb-2">
                            <div class="action-bar__element">
                                <template v-if="hasForms">
                                    <Dropdown class="SharpActionBar__forms-dropdown h-100" :text="l('action_bar.list.forms_dropdown')">
                                        <DropdownItem v-for="(form,key) in forms" @click="handleCreateFormSelected(form)" :key="key" >
                                            <ItemVisual :item="form" icon-class="fa-fw"/>{{ form.label }}
                                        </DropdownItem>
                                    </Dropdown>
                                </template>
                                <template v-else>
                                    <button class="SharpButton SharpButton--accent" @click="handleCreateButtonClicked">
                                        {{ l('action_bar.list.create_button') }}
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { Localization } from 'sharp/mixins';
    import { Search, ItemVisual, Dropdown, DropdownItem } from 'sharp-ui';
    import { FilterDropdown } from 'sharp-filters';

    export default {
        mixins: [Localization],
        components: {
            ItemVisual,
            Search,
            Dropdown,
            DropdownItem,
            FilterDropdown,
        },
        props: {
            search: String,
            filters: Array,
            filtersValues: Object,
            commands: Array,
            forms: Array,

            canCreate: Boolean,
            canReorder: Boolean,
            canSearch: Boolean,

            reorderActive: Boolean
        },
        data() {
            return {
                searchActive: false,
            }
        },
        computed: {
            hasForms() {
                return this.forms && this.forms.length > 0;
            },
            hasLeftControls() {
                return (this.filters || []).length > 0 || this.canSearch;
            },
        },
        methods: {
            handleSearchInput(search) {
                this.$emit('search-change', search);
            },
            handleSearchSubmitted() {
                this.$emit('search-submit');
            },
            handleFilterChanged(filter, value) {
                this.$emit('filter-change', filter, value);
            },
            handleReorderButtonClicked() {
                this.$emit('reorder-click');
                document.activeElement.blur();
            },
            handleReorderSubmitButtonClicked() {
                this.$emit('reorder-submit');
            },
            handleCommandSelected(command) {
                this.$emit('command', command);
            },
            handleCreateButtonClicked() {
                this.$emit('create');
            },
            handleCreateFormSelected(form) {
                this.$emit('create', form);
            },
        }
    }
</script>