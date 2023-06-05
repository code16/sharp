<template>
    <div class="action-bar my-3">
        <div class="row align-items-center gx-3">
            <div class="col">
                <template v-if="showBreadcrumb">
                    <Breadcrumb :items="breadcrumb" />
                </template>
            </div>
            <template v-if="locales && locales.length">
                <div class="col-auto">
                    <LocaleSelect
                        outline
                        right
                        :locale="currentLocale"
                        :locales="locales"
                        @change="handleLocaleChanged"
                    />
                </div>
            </template>
            <template v-if="hasState">
                <div class="col-auto">
                    <Dropdown toggle-class="bg-white" :show-caret="canChangeState" outline right :disabled="!canChangeState">
                        <template v-slot:text>
                            <StateIcon class="me-1" :color="state.color" style="vertical-align: -.125em" />
                            <span class="text-truncate">{{ state.label }}</span>
                        </template>
                        <template v-for="stateValue in stateValues">
                            <DropdownItem :active="state.value === stateValue.value" :key="stateValue.value" @mouseup.prevent.native="handleStateChanged(stateValue.value)">
                                <StateIcon class="me-1" :color="stateValue.color" style="vertical-align: -.125em" />
                                <span class="text-truncate">{{ stateValue.label }}</span>
                            </DropdownItem>
                        </template>
                    </Dropdown>
<!--                    <ModalSelect-->
<!--                        :title="l('modals.entity_state.edit.title')"-->
<!--                        :ok-title="l('modals.entity_state.edit.ok_button')"-->
<!--                        :value="state.value"-->
<!--                        :options="stateValues"-->
<!--                        size="sm"-->
<!--                        @change="handleStateChanged"-->
<!--                    >-->
<!--                        <template v-slot="{ on }">-->
<!--                            <Button-->
<!--                                class="btn&#45;&#45;opacity-1"-->
<!--                                :class="{ 'dropdown-toggle':canChangeState }"-->
<!--                                :disabled="!canChangeState"-->
<!--                                outline-->
<!--                                v-on="on"-->
<!--                            >-->
<!--                                -->
<!--                            </Button>-->
<!--                        </template>-->

<!--                        <template v-slot:item-prepend="{ option }">-->
<!--                            <StateIcon :color="option.color" />-->
<!--                        </template>-->
<!--                    </ModalSelect>-->
                </div>
            </template>
            <template v-if="hasCommands">
                <div class="col-auto">
                    <CommandsDropdown outline  :small="false" :commands="commands" @select="handleCommandSelected">
                        <template v-slot:text>
                            {{ l('entity_list.commands.instance.label') }}
                        </template>
                    </CommandsDropdown>
                </div>
            </template>
            <template v-if="canEdit">
                <div class="col-auto">
                    <Button :href="formUrl" :disabled="editDisabled">
                        {{ l('action_bar.show.edit_button') }}
                    </Button>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import {
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
    import LocaleSelect from "sharp-form/src/components/ui/LocaleSelect.vue";

    export default {
        mixins: [Localization],
        components: {
            LocaleSelect,
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
            currentLocale: String,
            locales: Array,
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
            handleLocaleChanged(locale) {
                this.$emit('locale-change', locale);
            },
        },
    }
</script>
