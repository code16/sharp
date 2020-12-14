<template>
    <ActionBar>
        <template v-slot:left>
            <template v-if="showBackButton">
                <a :href="backUrl" class="SharpButton SharpButton--secondary-accent">
                    {{ l('action_bar.show.back_button') }}
                </a>
            </template>
        </template>
        <template v-slot:right>
            <template v-if="canEdit">
                <a :href="formUrl" class="SharpButton SharpButton--accent">
                    {{ l('action_bar.show.edit_button') }}
                </a>
            </template>
        </template>
        <template v-slot:extras>
            <template v-if="showBreadcrumb">
                <Breadcrumb :items="breadcrumb" />
            </template>
        </template>
        <template v-slot:extras-right>
            <div class="row mx-n1">
                <template v-if="hasState">
                    <div class="col-auto px-1">
                        <Dropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--state" :disabled="!canChangeState">
                            <template v-slot:text>
                                <StateIcon :color="state.color" />
                                <span class="text-truncate">{{ state.label }}</span>
                            </template>
                            <template v-for="stateOptions in stateValues">
                                <DropdownItem
                                    @click="handleStateChanged(stateOptions.value)"
                                    :key="stateOptions.value"
                                >
                                    <StateIcon :color="stateOptions.color" />&nbsp;
                                    {{ stateOptions.label }}
                                </DropdownItem>
                            </template>
                        </Dropdown>
                    </div>
                </template>
                <template v-if="hasCommands">
                    <div class="col-auto px-1">
                        <CommandsDropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--commands"
                            :commands="commands"
                            @select="handleCommandSelected"
                        >
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
        },
        computed: {
            hasCommands() {
                return this.commands && this.commands.some(group => group && group.length > 0);
            },
            hasState() {
                return !!this.state;
            },
            showBreadcrumb() {
                return this.breadcrumb?.length > 1;
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
