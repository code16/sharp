<template>
    <div class="action-bar">
        <template v-if="hasOuterTitle">
            <div class="mb-2">
                <slot />
            </div>
        </template>
        <template v-if="ready && barVisible">
            <div class="row">
                <template v-if="hasLeftControls">
                    <div class="col-sm mb-2">
                        <template v-if="!reorderActive">
                            <div class="row gy-1 gx-2 gx-md-3">
                                <template v-for="filter in filters">
                                    <div class="col-auto mb-1">
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
                                    <div class="col-auto mb-1">
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
                    <div class="col-sm align-self-end">
                        <slot />
                    </div>
                </template>

                <template v-if="hasRightControls && !collapsed">
                    <div class="col-sm-auto mb-2">
                        <div class="row flex-nowrap justify-content-end g-2 gx-md-3">
                            <template v-if="canReorder">
                                <div class="col-auto">
                                    <template v-if="reorderActive">
                                        <div class="row gx-3">
                                            <div class="col-auto">
                                                <Button text @click="handleReorderButtonClicked">
                                                    {{ l('action_bar.list.reorder_button.cancel') }}
                                                </Button>
                                            </div>
                                            <div class="col-auto">
                                                <Button @click="handleReorderSubmitButtonClicked">
                                                    {{ l('action_bar.list.reorder_button.finish') }}
                                                </Button>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <Button text @click="handleReorderButtonClicked">
                                            {{ l('action_bar.list.reorder_button') }}
                                        </Button>
                                    </template>
                                </div>
                            </template>
                            <template v-if="canCreate && !reorderActive">
                                <div class="col-auto">
                                    <div class="action-bar__element">
                                        <template v-if="hasForms">
                                            <Dropdown variant="primary" :text="l('action_bar.list.forms_dropdown')">
                                                <template v-for="(form,key) in forms">
                                                    <DropdownItem  @click="handleCreateFormSelected(form)" :key="key" >
                                                        <ItemVisual :item="form" icon-class="fa-fw"/>{{ form.label }}
                                                    </DropdownItem>
                                                </template>
                                            </Dropdown>
                                        </template>
                                        <template v-else>
                                            <Button variant="primary" @click="handleCreateButtonClicked">
                                                {{ l('action_bar.list.create_button') }}
                                            </Button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>
</template>

<script>
    import { Localization } from 'sharp/mixins';
    import { Search, ItemVisual, Dropdown, DropdownItem, Button } from 'sharp-ui';
    import { FilterDropdown } from 'sharp-filters';

    export default {
        mixins: [Localization],
        components: {
            ItemVisual,
            Search,
            Dropdown,
            DropdownItem,
            FilterDropdown,
            Button,
        },
        props: {
            ready: Boolean,
            count: Number,
            search: String,
            hasSearchQuery: Boolean,
            filters: Array,
            filtersValues: Object,
            commands: Array,
            forms: Array,

            canCreate: Boolean,
            canReorder: Boolean,
            canSearch: Boolean,

            reorderActive: Boolean,

            // show field props
            collapsed: Boolean,
            hasActiveQuery: Boolean,
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
                return this.hasActiveQuery || this.count > 0 && (this.filters?.length > 0 || this.canSearch);
            },
            hasRightControls() {
                return this.canReorder || this.canCreate && !this.reorderActive;
            },
            hasOuterTitle() {
                return this.$slots.default && (!this.ready || this.hasLeftControls);
            },
            barVisible() {
                if(this.collapsed) {
                    return !this.hasLeftControls;
                }
                return true;
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
