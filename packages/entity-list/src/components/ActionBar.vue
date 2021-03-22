<template>
    <ActionBar
        class="SharpActionBarList"
        :class="{'SharpActionBarList--search-active':searchActive}"
        right-class="d-block"
    >
        <template v-slot:left>
            <div class="ui-font ui-font-size" style="font-weight: 600">
                <div class="row gx-2">
                    <template v-if="currentEntity">
                        <div class="col-auto">
                            <i class="fa" :class="currentEntity.icon"></i>
                        </div>
                    </template>

                    <div class="col">
                        <template v-if="currentEntity">
                            {{ currentEntity.label }} <span class="mx-1">&bull;</span>
                        </template>
                        <span class="text-content text-nowrap">{{ count }} {{ l('action_bar.list.items_count') }}</span>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:right>
            <div class="row justify-content-end flex-nowrap">
                <template v-if="canReorder">
                    <div class="col-auto" :class="{ 'd-none d-sm-block': searchActive }">
                        <template v-if="reorderActive">
                            <Button variant="light" large outline @click="handleReorderButtonClicked">
                                {{ l('action_bar.list.reorder_button.cancel') }}
                            </Button>
                            <Button variant="light" large @click="handleReorderSubmitButtonClicked">
                                {{ l('action_bar.list.reorder_button.finish') }}
                            </Button>
                        </template>
                        <template v-else>
                            <Button variant="light" large outline @click="handleReorderButtonClicked">
                                {{ l('action_bar.list.reorder_button') }}
                            </Button>
                        </template>
                    </div>
                </template>

                <template v-if="canCreate && !reorderActive">
                    <div class="col-auto" :class="{ 'd-none d-sm-block': searchActive }">
                        <template v-if="hasForms">
                            <Dropdown variant="light" large :text="l('action_bar.list.forms_dropdown')">
                                <template v-for="(form, key) in forms">
                                    <DropdownItem  @click="handleCreateFormSelected(form)" :key="key" >
                                        <ItemVisual :item="form" icon-class="fa-fw"/>{{ form.label }}
                                    </DropdownItem>
                                </template>
                            </Dropdown>
                        </template>
                        <template v-else>
                            <Button variant="light" large @click="handleCreateButtonClicked">
                                {{ l('action_bar.list.create_button') }}
                            </Button>
                        </template>
                    </div>
                </template>
            </div>
        </template>
        <template v-if="!reorderActive" v-slot:extras>
            <div class="row mx-n2">
                <template v-for="filter in filters">
                    <div class="col-auto px-2">
                        <FilterDropdown
                            :filter="filter"
                            :value="filtersValues[filter.key]"
                            @input="handleFilterChanged(filter, $event)"
                            :key="filter.id"
                        />
                    </div>
                </template>
            </div>
        </template>
        <template v-if="canSearch && !reorderActive" v-slot:extras-right>
            <div style="max-width: 300px">
                <Search
                    class="h-100"
                    :value="search"
                    :active.sync="searchActive"
                    :placeholder="l('action_bar.list.search.placeholder')"
                    @input="handleSearchInput"
                    @submit="handleSearchSubmitted"
                />
            </div>
        </template>
    </ActionBar>
</template>

<script>
    import { ActionBar, Dropdown,  DropdownItem, ItemVisual, Search, Button, } from 'sharp-ui';
    import { FilterDropdown } from 'sharp-filters';

    import { Localization } from 'sharp/mixins';

    export default {
        name: 'SharpActionBarList',

        mixins: [Localization],

        components : {
            ActionBar,
            Dropdown,
            DropdownItem,
            ItemVisual,
            FilterDropdown,
            Search,
            Button,
        },

        props: {
            count: Number,
            search: String,
            filters: Array,
            filtersValues: Object,
            forms: Array,

            canCreate: Boolean,
            canReorder: Boolean,
            canSearch: Boolean,

            reorderActive: Boolean
        },

        data() {
            return {
                searchActive: false
            }
        },
        computed: {
            hasForms() {
                return this.forms && this.forms.length > 0;
            },
            currentEntity() {
                return this.$store.state.currentEntity;
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
            handleCreateButtonClicked() {
                this.$emit('create');
            },
            handleCreateFormSelected(form) {
                this.$emit('create', form);
            }
        }
    }
</script>
