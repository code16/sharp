<template>
    <div>
        <ActionBar />
        <div class="row">
            <div class="col">
                <EntityListTitle :count="count" :search="search">
                    <Breadcrumb :items="breadcrumb" />
                </EntityListTitle>
            </div>
            <div class="col-auto">
                <div class="row justify-content-end flex-nowrap gx-3">
                    <template v-if="canReorder">
                        <template v-if="reorderActive">
                            <div class="col-auto">
                                <Button outline @click="handleReorderButtonClicked">
                                    {{ l('action_bar.list.reorder_button.cancel') }}
                                </Button>
                            </div>
                            <div class="col-auto">
                                <Button @click="handleReorderSubmitButtonClicked">
                                    {{ l('action_bar.list.reorder_button.finish') }}
                                </Button>
                            </div>
                        </template>
                        <template v-else>
                            <div class="col-auto">
                                <Button outline @click="handleReorderButtonClicked">
                                    {{ l('action_bar.list.reorder_button') }}
                                </Button>
                            </div>
                        </template>
                    </template>

                    <template v-if="hasCommands && !reorderActive">
                        <div class="col-auto">
                            <CommandsDropdown
                                class="bg-white"
                                outline
                                :commands="commands"
                                :disabled="reorderActive"
                                @select="handleCommandSelected"
                            >
                                <template v-slot:text>
                                    {{ l('entity_list.commands.entity.label') }}
                                </template>
                            </CommandsDropdown>
                        </div>
                    </template>

                    <template v-if="primaryCommand && !reorderActive">
                        <div class="col-auto">
                            <Button @click="handlePrimaryCommandClicked">
                                {{ primaryCommand.label }}
                            </Button>
                        </div>
                    </template>

                    <template v-if="canCreate && !reorderActive">
                        <div class="col-auto">
                            <template v-if="hasForms">
                                <MultiformDropdown
                                    :forms="forms"
                                    right
                                    @select="handleCreateFormSelected"
                                />
                            </template>
                            <template v-else>
                                <Button small @click="handleCreateButtonClicked">
                                    {{ l('action_bar.list.create_button') }}
                                </Button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { ActionBar, Dropdown,  DropdownItem, Search, Button, Breadcrumb } from 'sharp-ui';
    import { SharpFilter } from 'sharp-filters';

    import { Localization } from 'sharp/mixins';
    import MultiformDropdown from "./MultiformDropdown";
    import EntityListTitle from "./EntityListTitle";
    import { CommandsDropdown } from "sharp-commands";

    export default {
        name: 'SharpActionBarList',

        mixins: [Localization],

        components : {
            CommandsDropdown,
            EntityListTitle,
            MultiformDropdown,
            ActionBar,
            Dropdown,
            DropdownItem,
            SharpFilter,
            Search,
            Button,
            Breadcrumb,
        },

        props: {
            count: Number,
            search: String,
            filters: Array,
            filtersValues: Object,
            forms: Array,
            commands: Array,

            canCreate: Boolean,
            canReorder: Boolean,
            canSearch: Boolean,

            reorderActive: Boolean,
            breadcrumb: Array,
            showBreadcrumb: Boolean,
        },
        computed: {
            hasCommands() {
                return this.commands?.flat().length > 0;
            },
            primaryCommand() {
                return this.commands?.flat().find(command => command.primary);
            },
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
            handleCommandSelected(command) {
                this.$emit('command', command);
            },
        }
    }
</script>
