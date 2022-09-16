<template>
    <ActionBar class="SharpActionBarList">
        <template v-slot:left>
            <EntityListTitle :count="count" :search="search">
                <template v-if="currentEntity">
                    <div class="ui-font-size">
                        <div class="row align-items-center flex-nowrap gx-2">
                            <div class="col-auto">
                                <i class="fa" :class="currentEntity.icon"></i>
                            </div>
                            <div class="col">
                                <h1 class="ui-title-font mb-0" style="font-size: inherit">
                                    {{ currentEntity.label }}
                                </h1>
                            </div>
                        </div>
                    </div>
                </template>
            </EntityListTitle>
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
            <div class="row gx-2 gy-1">
                <template v-for="filter in filters">
                    <div class="col-auto">
                        <SharpFilter
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
    import { ActionBar, Dropdown,  DropdownItem, ItemVisual, Search, Button } from 'sharp-ui';
    import { SharpFilter } from 'sharp-filters';

    import { Localization } from 'sharp/mixins';
    import MultiformDropdown from "./MultiformDropdown";
    import EntityListTitle from "./EntityListTitle";

    export default {
        name: 'SharpActionBarList',

        mixins: [Localization],

        components : {
            EntityListTitle,
            MultiformDropdown,
            ActionBar,
            Dropdown,
            DropdownItem,
            ItemVisual,
            SharpFilter,
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
