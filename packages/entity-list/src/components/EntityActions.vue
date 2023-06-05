<template>
    <div class="SharpEntityList__actions">
        <div class="row align-items-center justify-content-end flex-nowrap gx-1">
            <template v-if="hasState">
                <div class="col-auto">
<!--                    style="--dropdown-menu-left: 36px" -->
                    <Dropdown
                        toggle-class="btn--opacity-1 btn--outline-hover"
                        small
                        :show-caret="false"
                        outline
                        right
                        :disabled="stateDisabled"
                        ref="stateDropdown"
                    >
                        <template v-slot:text>
                            <StateIcon :color="stateOptions.color" />
                        </template>
                        <template v-for="stateValue in config.state.values">
                            <DropdownItem :active="state === stateValue.value" :key="stateValue.value" @click="handleStateChanged(stateValue.value)">
                                <StateIcon class="me-1" :color="stateValue.color" style="vertical-align: -.125em" />
                                <span class="text-truncate">{{ stateValue.label }}</span>
                            </DropdownItem>
                        </template>
                    </Dropdown>
<!--                    <ModalSelect-->
<!--                        :title="l('modals.entity_state.edit.title')"-->
<!--                        :ok-title="l('modals.entity_state.edit.ok_button')"-->
<!--                        :value="state"-->
<!--                        :options="config.state.values"-->
<!--                        size="sm"-->
<!--                        @change="handleStateChanged"-->
<!--                        @update:visible="handleModalVisibilityChanged"-->
<!--                    >-->
<!--                        <template v-slot="{ on }">-->
<!--                            <Button-->
<!--                                class="btn&#45;&#45;opacity-1 btn&#45;&#45;outline-hover"-->
<!--                                variant="primary"-->
<!--                                small-->
<!--                                :disabled="stateDisabled"-->
<!--                                v-on="on"-->
<!--                            >-->
<!--                                <StateIcon :color="stateOptions.color" />-->
<!--                            </Button>-->
<!--                        </template>-->

<!--                        <template v-slot:item-prepend="{ option }">-->
<!--                            <StateIcon :color="option.color" />-->
<!--                        </template>-->
<!--                    </ModalSelect>-->
                </div>
            </template>
            <template v-if="hasActionsButton">
                <div class="col-auto">
                    <CommandsDropdown
                        class="SharpEntityList__commands-dropdown"
                        outline
                        :commands="commands"
                        :disabled="selecting"
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
                                        <StateIcon :color="stateOptions.color" />
                                    </div>
                                    <div class="col">
                                        <div class="row gx-2">
                                            <template v-if="!stateDisabled">
                                                <div class="col-auto">
                                                    {{ l('modals.entity_state.edit.title') }} :
                                                </div>
                                            </template>
                                            <div class="col-auto">
                                                {{ stateOptions.label }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </DropdownItem>
                            <DropdownSeparator />
                        </template>
                    </CommandsDropdown>
                </div>
            </template>
            <template v-else-if="hasState">
                <div class="col" style="min-width: 0">
                    <div class="ui-font text-muted text-start text-truncate mw-100 fs-8" ref="stateLabel">
                        {{ stateOptions.label }}
                    </div>
                    <Tooltip :target="() => $refs.stateLabel" overflow-only>
                        {{ stateOptions.label }}
                    </Tooltip>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { lang } from "sharp";
    import { CommandsDropdown } from "sharp-commands";
    import {DropdownSeparator, DropdownItem, StateIcon, ModalSelect, Button, Tooltip, Dropdown} from "sharp-ui";

    export default {
        components: {
            Dropdown,
            DropdownItem,
            DropdownSeparator,
            CommandsDropdown,
            StateIcon,
            ModalSelect,
            Button,
            Tooltip,
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
        },
        data() {
            return {
                stateModalVisible: false,
            }
        },
        computed: {
            hasActionsButton() {
                return this.hasCommands || this.hasState;
            },
        },
        methods: {
            l:lang,
            handleStateChanged(state) {
                this.$emit('state-change', state);
            },
            handleCommandRequested(command) {
                this.$emit('command', command);
            },
            handleModalVisibilityChanged(visible) {
                this.$emit('state-choosing', visible);
            },
            async handleStateDropdownClicked() {
                this.$emit('state-choosing', true);
                await this.$nextTick();
                this.$refs.stateDropdown.show();
                // setTimeout(() => {
                //     this.stateModalVisible = true;
                //     this.$el.querySelector('.commands-toggle').click();
                // }, 0);
            },
        },
    }
</script>
