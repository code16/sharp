<template>
    <ActionBar>
        <template v-slot:left>
            <template v-if="showBackButton">
                <Button :href="backUrl" outline variant="light" large>
                    {{ l('action_bar.show.back_button') }}
                </Button>
            </template>
        </template>
        <template v-slot:right>
            <template v-if="canEdit">
                <Button :href="formUrl" variant="light" large>
                    {{ l('action_bar.show.edit_button') }}
                </Button>
            </template>
        </template>
        <template v-slot:extras>
            <template v-if="showBreadcrumb">
                <Breadcrumb :items="breadcrumb" />
            </template>
        </template>
        <template v-slot:extras-right>
            <div class="row gx-3">
                <template v-if="hasState">
                    <div class="col-auto">
                        <ModalSelect
                            :title="l('modals.entity_state.edit.title')"
                            :ok-title="l('modals.entity_state.edit.ok_button')"
                            :value="state.value"
                            :options="stateValues"
                            size="sm"
                            @change="handleStateChanged"
                        >
                            <template v-slot="{ on }">
                                <Button
                                    class="btn--opacity-1"
                                    :class="{ 'dropdown-toggle':canChangeState }"
                                    :disabled="!canChangeState"
                                    text
                                    small
                                    v-on="on"
                                >
                                    <StateIcon class="me-1" :color="state.color" style="vertical-align: -.125em" />
                                    <span class="text-truncate">{{ state.label }}</span>
                                </Button>
                            </template>

                            <template v-slot:item-prepend="{ option }">
                                <StateIcon :color="option.color" />
                            </template>
                        </ModalSelect>
                    </div>
                </template>
                <template v-if="hasCommands">
                    <div class="col-auto">
                        <CommandsDropdown :commands="commands" @select="handleCommandSelected">
                            <template v-slot:text>
                                {{ l('entity_list.commands.instance.label') }}
                            </template>
                        </CommandsDropdown>
                    </div>
                </template>
            </div>
        </template>
    </ActionBar>
</template>

<script>
    import {
        ActionBar,
        Dropdown,
        DropdownItem,
        StateIcon,
        Breadcrumb,
        Button,
        ModalSelect,
    } from 'sharp-ui';

    import {
        CommandsDropdown
    } from 'sharp-commands';

    import { Localization } from "sharp/mixins";

    export default {
        mixins: [Localization],
        components: {
            ActionBar,
            Breadcrumb,
            CommandsDropdown,
            Dropdown,
            DropdownItem,
            StateIcon,
            Button,
            ModalSelect,
        },
        props: {
            commands: Array,
            formUrl: String,
            backUrl: String,
            canEdit: Boolean,
            canChangeState: Boolean,
            showBackButton: Boolean,
            state: Object,
            stateValues: Array,
            breadcrumb: Array,
            showBreadcrumb: Boolean,
        },
        computed: {
            hasCommands() {
                return this.commands && this.commands.some(group => group && group.length > 0);
            },
            hasState() {
                return !!this.state;
            },
        },
        methods: {
            handleEditButtonClicked() {
                this.$emit('edit');
            },
            handleCommandSelected(command) {
                this.$emit('command', command);
            },
            handleStateChanged(state) {
                this.$emit('state-change', state);
            },
        }
    }
</script>
