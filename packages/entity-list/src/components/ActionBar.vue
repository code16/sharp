<template>
    <div class="mb-4">
        <ActionBar />
        <div class="row">
            <div class="col">
                <EntityListTitle :count="count" :search="search">
                    <Breadcrumb :items="breadcrumb" />
                </EntityListTitle>
            </div>
            <div class="col-auto">
                <div class="row justify-content-end flex-nowrap gx-3">
                    <template v-if="canReorder && !selecting">
                        <template v-if="reordering">
                            <div class="col-auto">
                                <Button outline @click="handleReorderButtonClicked">
                                    {{ l('action_bar.list.reorder_button') }}
                                </Button>
                            </div>
                        </template>
                        <template v-else>
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
                    </template>

                    <template v-if="canSelect && !reordering">
                        <template v-if="selecting">
                            <div class="col-auto">
                                <Button key="cancel" outline small @click="handleSelectButtonClicked">
                                    {{ l('action_bar.list.reorder_button.cancel') }}
                                </Button>
                            </div>
                        </template>
                       <template v-else>
                           <div class="col-auto">
                               <Button key="select" outline small @click="handleSelectButtonClicked">
                                   {{ l('action_bar.list.select_button') }}
                               </Button>
                           </div>
                       </template>
                    </template>

                    <template v-if="hasCommands && !reordering">
                        <div class="col-auto">
                            <CommandsDropdown
                                class="bg-white"
                                :outline="!selecting"
                                :commands="commands"
                                :disabled="reordering"
                                @select="handleCommandSelected"
                            >
                                <template v-slot:text>
                                    {{ l('entity_list.commands.entity.label') }}
                                </template>
                            </CommandsDropdown>
                        </div>
                    </template>

                    <template v-if="primaryCommand && !reordering && !selecting">
                        <div class="col-auto">
                            <Button @click="handlePrimaryCommandClicked">
                                {{ primaryCommand.label }}
                            </Button>
                        </div>
                    </template>

                    <template v-if="canCreate && !reordering && !selecting">
                        <div class="col-auto">
                            <template v-if="hasForms">
                                <MultiformDropdown
                                    :forms="forms"
                                    right
                                    @select="handleCreateFormSelected"
                                />
                            </template>
                            <template v-else>
                                <Button :disabled="reordering || selecting" small @click="handleCreateButtonClicked">
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
            canSelect: Boolean,

            reordering: Boolean,
            selecting: Boolean,

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
            handleSelectButtonClicked() {
                this.$emit('select-click');
            },
        }
    }
</script>
