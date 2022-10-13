<template>
    <ActionBar>
        <template v-slot:left>
            <div class="row align-items-center gx-4">
                <template v-if="showBackButton">
                    <div class="col-auto">
                        <Button :href="backUrl" outline variant="light" large>
                            {{ l('action_bar.show.back_button') }}
                        </Button>
                    </div>
                </template>
                <template v-if="title">
                    <div class="col">
                        <div class="h5 mb-0" :class="{ 'opacity-0': !showTitle }" style="transition: opacity .2s ease-in-out">
                            {{ title }}
                        </div>
                    </div>
                </template>
            </div>

        </template>
        <template v-slot:right>
            <template v-if="canEdit">
                <Button :href="formUrl" :disabled="editDisabled" variant="light" large>
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
            editDisabled: Boolean,
            canChangeState: Boolean,
            showBackButton: Boolean,
            state: Object,
            stateValues: Array,
            breadcrumb: Array,
            showBreadcrumb: Boolean,
        },
        data() {
            return {
                showTitle: false,
            }
        },
        computed: {
            hasCommands() {
                return this.commands?.flat().length > 0;
            },
            hasState() {
                return !!this.state;
            },
            title() {
                return this.breadcrumb && this.showBreadcrumb
                    ? this.breadcrumb[this.breadcrumb.length - 1]?.name
                    : null;
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
            handleScroll() {
                this.showTitle = document.querySelector('.ShowPage__content').getBoundingClientRect().top < 0;
            },
        },
        mounted() {
            window.addEventListener('scroll', this.handleScroll);
        },
    }
</script>
