<script setup lang="ts">
    import { __ } from "@/utils/i18n";
</script>

<template>
    <div class="SharpEntityList__actions">
        <div class="row align-items-center justify-content-end flex-nowrap gx-1">
            <template v-if="hasState">
                <div class="col-auto">
                    <Dropdown
                        toggle-class="btn--opacity-1 btn--outline-hover"
                        small
                        :show-caret="false"
                        outline
                        right
                        :disabled="stateDisabled"
                        :title="stateOptions ? stateOptions.label : state"
                        ref="stateDropdown"
                    >
                        <template v-slot:text>
                            <StateIcon :color="stateOptions ? stateOptions.color : '#fff'" />
                        </template>
                        <template v-for="stateValue in config.state.values" :key="stateValue.value">
                            <DropdownItem :active="state === stateValue.value" @click="handleStateChanged(stateValue.value)">
                                <StateIcon class="me-1" :color="stateValue.color" style="vertical-align: -.125em" />
                                <span class="text-truncate">{{ stateValue.label }}</span>
                            </DropdownItem>
                        </template>
                    </Dropdown>
                </div>
            </template>
            <template v-if="hasActionsButton">
                <div class="col-auto">
                    <CommandsDropdown
                        class="SharpEntityList__commands-dropdown"
                        outline
                        :commands="commands"
                        :has-state="hasState"
                        :toggle-class="['p-1 commands-toggle', { 'opacity-50': selecting }]"
                        :show-caret="false"
                        @select="handleCommandRequested"
                    >
                        <template v-slot:text>
                            <!-- EllipsisVerticalIcon -- @heroicons/vue/20 -->
                            <svg class="d-block" width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                            </svg>
                        </template>
                        <template v-if="hasState" v-slot:prepend>
                            <DropdownItem :disabled="stateDisabled" @click.prevent="handleStateDropdownClicked">
                                <div class="row align-items-center gx-2 flex-nowrap">
                                    <div class="col-auto">
                                        <StateIcon :color="stateOptions ? stateOptions.color : '#fff'" />
                                    </div>
                                    <div class="col">
                                        <div class="row gx-2">
                                            <template v-if="!stateDisabled">
                                                <div class="col-auto">
                                                    {{ __('sharp::modals.entity_state.edit.title') }} :
                                                </div>
                                            </template>
                                            <div class="col-auto">
                                                {{ stateOptions ? stateOptions.label : state }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </DropdownItem>
                            <DropdownSeparator />
                        </template>
                        <template v-if="canDelete" v-slot:append>
                            <DropdownSeparator />
                            <DropdownItem link-class="text-danger" @click="handleDeleteClicked">
                                {{ __('sharp::action_bar.form.delete_button') }}
                            </DropdownItem>
                        </template>
                    </CommandsDropdown>
                </div>
            </template>
        </div>
    </div>
</template>

<script lang="ts">
    import { CommandsDropdown } from "@sharp/commands";
    import { DropdownSeparator, DropdownItem, StateIcon, ModalSelect, Button, Dropdown } from "@sharp/ui";

    export default {
        components: {
            Dropdown,
            DropdownItem,
            DropdownSeparator,
            CommandsDropdown,
            StateIcon,
            ModalSelect,
            Button,
        },
        props: {
            config: Object,
            hasState: Boolean,
            state: [String, Number],
            stateDisabled: Boolean,
            stateOptions: Object,
            hasCommands: Boolean,
            commands: Array,
            selecting: Boolean,
            canDelete: Boolean,
        },
        data() {
            return {
                stateModalVisible: false,
            }
        },
        computed: {
            hasActionsButton() {
                return this.hasCommands || this.hasState || this.canDelete;
            },
        },
        methods: {
            handleStateChanged(state) {
                this.$emit('state-change', state);
            },
            handleCommandRequested(command) {
                this.$emit('command', command);
            },
            handleDeleteClicked() {
                this.$emit('delete');
            },
            async handleStateDropdownClicked() {
                await this.$nextTick();
                this.$refs.stateDropdown.show();
            },
        },
    }
</script>
