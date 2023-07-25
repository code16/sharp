<template>
    <div class="action-bar mt-4 mb-3">
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
                    <Dropdown :toggle-class="{ 'bg-white': !canChangeState }" :show-caret="canChangeState" outline right :disabled="!canChangeState">
                        <template v-slot:text>
                            <StateIcon class="me-1" :color="stateOptions ? stateOptions.color : '#fff'" style="vertical-align: -.125em" />
                            <span class="text-truncate">{{ stateOptions ? stateOptions.label : state }}</span>
                        </template>
                        <template v-for="stateValue in stateValues" :key="stateValue.value">
                            <DropdownItem :active="state === stateValue.value" @mouseup.prevent.native="handleStateChanged(stateValue.value)">
                                <StateIcon class="me-1" :color="stateValue.color" style="vertical-align: -.125em" />
                                <span class="text-truncate">{{ stateValue.label }}</span>
                            </DropdownItem>
                        </template>
                    </Dropdown>
                </div>
            </template>
            <template v-if="hasCommands || canDelete">
                <div class="col-auto">
                    <CommandsDropdown outline :small="false" :commands="commands" @select="handleCommandSelected">
                        <template v-slot:text>
                            {{ l('entity_list.commands.instance.label') }}
                        </template>
                        <template v-if="canDelete" v-slot:append>
                            <DropdownSeparator />
                            <DropdownItem link-class="text-danger" @click="handleDeleteClicked">
                                {{ l('action_bar.form.delete_button') }}
                            </DropdownItem>
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
    ModalSelect, DropdownSeparator,
} from 'sharp-ui';

    import {
        CommandsDropdown
    } from 'sharp-commands';

    import { Localization } from "sharp/mixins";
    import LocaleSelect from "sharp-form/src/components/ui/LocaleSelect.vue";

    export default {
        mixins: [Localization],
        components: {
            DropdownSeparator,
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
            state: [String, Number],
            stateOptions: Object,
            stateValues: Array,
            breadcrumb: Array,
            showBreadcrumb: Boolean,
            currentLocale: String,
            locales: Array,
            canDelete: Boolean,
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
            handleDeleteClicked() {
                this.$emit('delete');
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
