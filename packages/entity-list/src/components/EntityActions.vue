<template>
    <div class="row align-items-center flex-nowrap gx-1">
        <template v-if="hasState">
            <div class="col-auto">
                <ModalSelect
                    :visible.sync="stateModalVisible"
                    :title="l('modals.entity_state.edit.title')"
                    :ok-title="l('modals.entity_state.edit.ok_button')"
                    :value="state"
                    :options="config.state.values"
                    size="sm"
                    @change="handleStateChanged"
                    @update:visible="handleSelecting"
                >
                    <template v-slot="{ on }">
                        <Button
                            class="btn--opacity-1 btn--outline-hover"
                            variant="primary"
                            small
                            :disabled="stateDisabled"
                            v-on="on"
                        >
                            <StateIcon :color="stateOptions.color" />
                        </Button>
                    </template>

                    <template v-slot:item-prepend="{ option }">
                        <StateIcon :color="option.color" />
                    </template>
                </ModalSelect>
            </div>
        </template>
        <template v-if="hasActionsButton">
            <div class="col-auto">
                <CommandsDropdown
                    class="SharpEntityList__commands-dropdown"
                    outline
                    :commands="commands"
                    :has-state="hasState"
                    @select="handleCommandRequested"
                >
                    <template v-slot:text>
                        {{ l('entity_list.commands.instance.label') }}
                    </template>
                    <template v-if="hasState" v-slot:prepend>
                        <DropdownItem :disabled="stateDisabled" @click="handleStateDropdownClicked">
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
                <div class="ui-font btn-sm text-muted text-start text-truncate mw-100 px-0" ref="stateLabel">
                    {{ stateOptions.label }}
                </div>
                <Tooltip :target="() => $refs.stateLabel" overflow-only>
                    {{ stateOptions.label }}
                </Tooltip>
            </div>
        </template>
    </div>
</template>

<script>
    import { lang } from "sharp";
    import { CommandsDropdown } from "sharp-commands";
    import { DropdownSeparator, DropdownItem, StateIcon, ModalSelect, Button, Tooltip } from "sharp-ui";

    export default {
        components: {
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
        },
        data() {
            return {
                stateModalVisible: false,
            }
        },
        computed: {
            hasActionsButton() {
                return this.hasCommands || this.hasState && !this.stateDisabled;
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
            handleSelecting(selecting) {
                this.$emit('selecting', selecting);
            },
            handleStateDropdownClicked() {
                this.$emit('selecting', true);
                this.stateModalVisible = true;
            }
        },
    }
</script>
