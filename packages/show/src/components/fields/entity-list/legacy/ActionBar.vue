<script setup lang="ts">
    import { __ } from "@/utils/i18n";
</script>

<template>
    <div class="action-bar"
        :class="{ 'position-sticky': sticky }"
        v-sticky="sticky"
        @stuck-change="stuck = $event.detail"
    >
        <div class="position-relative">
            <template v-if="hasOuterTitle">
                <div class="mb-2">
                    <slot />
                </div>
            </template>
            <template v-if="ready && barVisible">
                <div class="row align-items-end align-content-end" data-sticky-anchor>
                    <div class="col-sm align-self-end">
                        <slot />
                    </div>

                    <template v-if="hasRightControls && !collapsed">
                        <div class="col-sm-auto mb-2">
                            <div class="row flex-nowrap justify-content-end g-2 gx-md-3">
                                <template v-if="canReorder">
                                    <div class="col-auto">
                                        <template v-if="reordering">
                                            <div class="row gx-3">
                                                <div class="col-auto">
                                                    <Button text small @click="handleReorderButtonClicked">
                                                        {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                                    </Button>
                                                </div>
                                                <div class="col-auto">
                                                    <Button variant="primary" small @click="handleReorderSubmitButtonClicked">
                                                        {{ __('sharp::action_bar.list.reorder_button.finish') }}
                                                    </Button>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <Button text small @click="handleReorderButtonClicked">
                                                {{ __('sharp::action_bar.list.reorder_button') }}
                                            </Button>
                                        </template>
                                    </div>
                                </template>

                                <template v-if="canSelect && !reordering">
                                    <template v-if="selecting">
                                        <div class="col-auto">
                                            <Button key="cancel" small outline @click="handleSelectCancelled">
                                                {{ __('sharp::action_bar.list.reorder_button.cancel') }}
                                            </Button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="col-auto">
                                            <Button key="select" small outline @click="handleSelectButtonClicked">
                                                {{ __('sharp::action_bar.list.select_button') }}
                                            </Button>
                                        </div>
                                    </template>
                                </template>

                                <template v-if="hasDropdownCommands && !reordering">
                                    <div class="col-auto">
                                        <CommandsDropdown
                                            class="bg-white"
                                            small
                                            :outline="!selecting"
                                            :commands="dropdownCommands"
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

                                <template v-if="primaryCommand && !reordering">
                                    <div class="col-auto">
                                        <Button variant="primary" small @click="handleCommandSelected(primaryCommand)">
                                            {{ primaryCommand.label }}
                                        </Button>
                                    </div>
                                </template>

                                <template v-if="canCreate && !reordering">
                                    <div class="col-auto">
                                        <div class="action-bar__element">
                                            <template v-if="hasForms">
                                                <MultiformDropdown
                                                    :forms="forms"
                                                    variant="primary"
                                                    right
                                                    small
                                                    @select="handleCreateFormSelected"
                                                />
                                            </template>
                                            <template v-else>
                                                <Button variant="primary" small @click="handleCreateButtonClicked">
                                                    {{ __('sharp::action_bar.list.create_button') }}
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
    </div>
</template>

<script lang="ts">
    import { Button } from '@sharp/ui';
    import { MultiformDropdown } from "@sharp/entity-list";
    // import { sticky } from "sharp/directives";
    import { CommandsDropdown } from "@sharp/commands";

    export default {
        components: {
            CommandsDropdown,
            MultiformDropdown,
            Button,
        },
        props: {
            ready: Boolean,
            count: Number,
            primaryCommand: Object,
            dropdownCommands: Array,

            forms: Array,

            canCreate: Boolean,
            canReorder: Boolean,
            canSelect: Boolean,

            reordering: Boolean,
            selecting: Boolean,
            selectedCount: Number,

            // show field props
            collapsed: Boolean,
            hasActiveQuery: Boolean,
            sticky: Boolean,
        },
        data() {
            return {
                stuck: false,
            }
        },
        computed: {
            hasForms() {
                return this.forms && this.forms.length > 0;
            },
            hasLeftControls() {
                return false;
                // return this.hasActiveQuery || this.count > 0 && (this.filters?.length > 0 || this.canSearch);
            },
            hasRightControls() {
                return this.canReorder || this.canCreate || !!this.primaryCommand || this.hasDropdownCommands;
            },
            hasOuterTitle() {
                return this.$slots.default && (!this.ready || this.hasLeftControls);
            },
            hasDropdownCommands() {
                return this.dropdownCommands?.flat().length > 0;
            },
            barVisible() {
                if(this.collapsed) {
                    return !this.hasLeftControls;
                }
                return true;
            },
        },
        methods: {
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
            handleSelectButtonClicked() {
                this.$emit('select-click');
            },
            handleSelectCancelled() {
                this.$emit('select-cancel');
            },
        },
        directives: {
            sticky,
        },
    }
</script>
