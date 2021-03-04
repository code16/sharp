<template>
    <ActionBar
        class="SharpActionBarList"
        :class="{'SharpActionBarList--search-active':searchActive}"
        right-class="d-block"
    >
        <template v-slot:left>
            <span class="text-content text-nowrap">{{ count }} {{ l('action_bar.list.items_count') }}</span>
        </template>
        <template v-slot:right>
            <div class="row justify-content-end flex-nowrap">
                <template v-if="canSearch && !reorderActive">
                    <div class="col col-lg-auto" style="max-width: 300px">
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

                <template v-if="canReorder">
                    <div class="col-auto" :class="{ 'd-none d-sm-block': searchActive }">
                        <template v-if="reorderActive">
                            <Button variant="light" outline @click="handleReorderButtonClicked">
                                {{ l('action_bar.list.reorder_button.cancel') }}
                            </Button>
                            <Button variant="light" @click="handleReorderSubmitButtonClicked">
                                {{ l('action_bar.list.reorder_button.finish') }}
                            </Button>
                        </template>
                        <template v-else>
                            <Button variant="light" outline @click="handleReorderButtonClicked">
                                {{ l('action_bar.list.reorder_button') }}
                            </Button>
                        </template>
                    </div>
                </template>

                <template v-if="canCreate && !reorderActive">
                    <div class="col-auto" :class="{ 'd-none d-sm-block': searchActive }">
                        <template v-if="hasForms">
                            <Dropdown class="SharpActionBar__forms-dropdown h-100" :text="l('action_bar.list.forms_dropdown')">
                                <DropdownItem v-for="(form,key) in forms" @click="handleCreateFormSelected(form)" :key="key" >
                                    <ItemVisual :item="form" icon-class="fa-fw"/>{{ form.label }}
                                </DropdownItem>
                            </Dropdown>
                        </template>
                        <template v-else>
                            <Button variant="light" @click="handleCreateButtonClicked">
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
        <template v-if="!reorderActive" v-slot:extras-right>
            <template v-if="hasCommands">
                <CommandsDropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--commands"
                    :commands="commands"
                    @select="handleCommandSelected"
                >
                    <template v-slot:text>
                        {{ l('entity_list.commands.entity.label') }}
                    </template>
                </CommandsDropdown>
            </template>
        </template>
    </ActionBar>
</template>

<script>
    import { ActionBar, Dropdown,  DropdownItem, ItemVisual, Search, Button, } from 'sharp-ui';
    import { FilterDropdown } from 'sharp-filters';
    import { CommandsDropdown } from 'sharp-commands';

    import { Localization } from 'sharp/mixins';

    export default {
        name: 'SharpActionBarList',

        mixins: [Localization],

        components : {
            ActionBar,
            Dropdown,
            DropdownItem,
            ItemVisual,
            CommandsDropdown,
            FilterDropdown,
            Search,
            Button,
        },

        props: {
            count: Number,
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
                searchActive: false
            }
        },
        computed: {
            hasForms() {
                return this.forms && this.forms.length > 0;
            },
            hasCommands() {
                return this.commands && this.commands.some(group => group && group.length > 0);
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
            }
        }
    }
</script>
