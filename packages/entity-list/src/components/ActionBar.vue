<template>
    <ActionBar class="SharpActionBarList">
        <template v-slot:left>
            <div class="ui-font-size">
                <div class="row align-items-center gx-1">
                    <template v-if="currentEntity">
                        <div class="col-sm-auto">
                            <div class="ui-title-font">
                                <div class="row flex-nowrap gx-2">
                                    <div class="col-auto">
                                        <i class="fa" :class="currentEntity.icon"></i>
                                    </div>
                                    <div class="col">
                                        {{ currentEntity.label }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div class="col">
                        <div class="small" style="opacity: .75">
                            &bull;
                            <span class="text-nowrap">{{ count }} {{ l('action_bar.list.items_count') }}</span>
                            <template v-if="search">
                                <span>
                                    &bull; {{ searchLabel }}
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:right>
            <div class="row justify-content-end flex-nowrap gx-3 gx-md-4">
                <template v-if="canReorder">
                    <template v-if="reorderActive">
                        <div class="col-auto">
                            <Button variant="light" large outline @click="handleReorderButtonClicked">
                                {{ l('action_bar.list.reorder_button.cancel') }}
                            </Button>
                        </div>
                        <div class="col-auto">
                            <Button variant="light" large @click="handleReorderSubmitButtonClicked">
                                {{ l('action_bar.list.reorder_button.finish') }}
                            </Button>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-auto">
                            <Button variant="light" large outline @click="handleReorderButtonClicked">
                                {{ l('action_bar.list.reorder_button') }}
                            </Button>
                        </div>
                    </template>
                </template>

                <template v-if="primaryCommand && !reorderActive">
                    <div class="col-auto">
                        <Button variant="light" large @click="handlePrimaryCommandClicked">
                            {{ primaryCommand.label }}
                        </Button>
                    </div>
                </template>

                <template v-if="canCreate && !reorderActive">
                    <div class="col-auto">
                        <template v-if="hasForms">
                            <MultiformDropdown
                                :forms="forms"
                                variant="light"
                                large
                                right
                                @select="handleCreateFormSelected"
                            />
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
        <template v-slot:extras>
            <div class="row mx-n2">
                <template v-for="filter in filters">
                    <div class="col-auto px-2">
                        <FilterDropdown
                            :filter="filter"
                            :value="filtersValues[filter.key]"
                            :disabled="reorderActive"
                            @input="handleFilterChanged(filter, $event)"
                            :key="filter.id"
                        />
                    </div>
                </template>
            </div>
        </template>
        <template v-if="canSearch" v-slot:extras-right>
            <div style="max-width: 300px">
                <Search
                    class="h-100"
                    :value="search"
                    :placeholder="l('action_bar.list.search.placeholder')"
                    :disabled="reorderActive"
                    @submit="handleSearchSubmitted"
                />
            </div>
        </template>
    </ActionBar>
</template>

<script>
    import { lang } from 'sharp';
    import { ActionBar, Dropdown,  DropdownItem, ItemVisual, Search, Button, } from 'sharp-ui';
    import { FilterDropdown } from 'sharp-filters';

    import { Localization } from 'sharp/mixins';
    import MultiformDropdown from "./MultiformDropdown";

    export default {
        name: 'SharpActionBarList',

        mixins: [Localization],

        components : {
            MultiformDropdown,
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
            primaryCommand: Object,

            canCreate: Boolean,
            canReorder: Boolean,
            canSearch: Boolean,

            reorderActive: Boolean
        },
        computed: {
            hasForms() {
                return this.forms && this.forms.length > 0;
            },
            currentEntity() {
                return this.$store.state.currentEntity;
            },
            searchLabel() {
                return lang('action_bar.list.search.title').replace(':search', this.search);
            },
        },
        methods: {
            handleSearchSubmitted(search) {
                this.$emit('search-submit', search);
            },
            handleFilterChanged(filter, value) {
                this.$emit('filter-change', filter, value);
            },
            handlePrimaryCommandClicked() {
                this.$emit('command', this.primaryCommand);
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
            },
        }
    }
</script>
