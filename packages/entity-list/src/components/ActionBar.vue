<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { config } from "@/utils/config";
</script>

<template>
    <div class="action-bar mt-4 mb-3 position-sticky ShowEntityListField__action-bar"
        v-sticky
        @stuck-change="stuck = $event.detail"
    >
        <div class="row align-items-center">
            <div class="col position-relative">
                <template v-if="config('sharp.display_breadcrumb')">
                    <EntityListTitle :count="count">
                        <Breadcrumb :items="breadcrumb" />
                    </EntityListTitle>
                </template>
            </div>
            <div class="col-auto position-relative">
                <div class="row justify-content-end flex-nowrap gx-3">
                    <template v-if="canReorder && !selecting">
                        <template v-if="reordering">
                            <div class="col-auto">
                                <Button outline @click="handleReorderButtonClicked">
                                    {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                </Button>
                            </div>
                            <div class="col-auto">
                                <Button @click="handleReorderSubmitButtonClicked">
                                    {{ __('sharp::action_bar.list.reorder_button.finish') }}
                                </Button>
                            </div>
                        </template>
                        <template v-else>
                            <div class="col-auto">
                                <Button outline @click="handleReorderButtonClicked">
                                    {{ __('sharp::action_bar.list.reorder_button') }}
                                </Button>
                            </div>
                        </template>
                    </template>

                    <template v-if="canSelect && !reordering">
                        <template v-if="selecting">
                            <div class="col-auto">
                                <Button key="cancel" outline @click="handleSelectCancelled">
                                    {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                </Button>
                            </div>
                        </template>
                        <template v-else>
                            <div class="col-auto">
                                <Button key="select" outline @click="handleSelectButtonClicked">
                                    {{ __('sharp::action_bar.list.select_button') }}
                                </Button>
                            </div>
                        </template>
                    </template>

                    <template v-if="hasDropdownCommands && !reordering">
                        <div class="col-auto">
                            <CommandsDropdown
                                class="bg-white"
                                :small="false"
                                :outline="!selecting"
                                :commands="commands"
                                :disabled="reordering"
                                :selecting="selecting"
                                @select="handleCommandSelected"
                            >
                                <template v-slot:text>
                                    {{ __('sharp::entity_list.commands.entity.label') }}
                                    <template v-if="selecting">
                                        ({{ selectedCount }} selected)
                                    </template>
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
                                <Button :disabled="reordering || selecting" @click="handleCreateButtonClicked">
                                    {{ __('sharp::action_bar.list.create_button') }}
                                </Button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import { Dropdown,  DropdownItem, Search, Button, Breadcrumb } from '@sharp/ui';
    import { SharpFilter } from '@sharp/filters';

    import MultiformDropdown from "./MultiformDropdown.vue";
    import EntityListTitle from "./EntityListTitle.vue";
    import { CommandsDropdown } from "@sharp/commands";
    import { sticky } from "sharp/directives";

    export default {
        name: 'SharpActionBarList',

        components : {
            CommandsDropdown,
            EntityListTitle,
            MultiformDropdown,
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
            selectedCount: Number,

            breadcrumb: Array,
        },
        data() {
            return {
                stuck: false,
            }
        },
        computed: {
            hasDropdownCommands() {
                return this.commands?.flat().filter(command => !command.primary).length > 0;
            },
            primaryCommand() {
                return this.commands?.flat().find(command => command.primary);
            },
            hasForms() {
                return this.forms && this.forms.length > 0;
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
            handleSelectCancelled() {
                this.$emit('select-cancel');
            },
        },
        directives: {
            sticky
        },
    }
</script>
