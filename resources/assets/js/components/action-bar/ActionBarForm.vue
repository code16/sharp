<template>
    <sharp-action-bar>
        <template slot="left">
            <button v-if="backButton" class="SharpButton SharpButton--secondary" @click="emitAction('cancel')">
                {{ l('action_bar.form.back_button') }}
            </button>
            <sharp-locale-selector v-if="locales" @input="l=>emitAction('localeChanged',l)" :value="locale" :locales="locales"></sharp-locale-selector>
        </template>
        <template slot="right">
            <button v-if="!backButton" class="SharpButton SharpButton--secondary" @click="emitAction('cancel')">
                {{ l('action_bar.form.cancel_button') }}
            </button>
            <button v-if="submitButtonVisible" class="SharpButton SharpButton--primary" @click="emitAction('submit')">
                {{ l('action_bar.form.submit_button') }}
            </button>
            <template v-if="actionsDropdownVisible">
                <sharp-dropdown  class="SharpActionBar__actions-dropdown">
                    <sharp-dropdown-item @click="emitAction('delete')">{{ l('action_bar.form.delete_button') }}</sharp-dropdown-item>
                </sharp-dropdown>
            </template>
        </template>
    </sharp-action-bar>
</template>

<script>
    import ActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';

    import LocaleSelector from '../LocaleSelector';
    import { Dropdown, DropdownItem } from '../dropdown';

    import { Localization, ActionEvents } from '../../mixins';

    export default {
        name: 'SharpActionBarForm',
        mixins: [ActionBarMixin, ActionEvents, Localization],
        components: {
            [ActionBar.name]:ActionBar,
            [LocaleSelector.name]:LocaleSelector,
            [Dropdown.name]: Dropdown,
            [DropdownItem.name]: DropdownItem
        },
        data() {
            return {
                locales:null,
                locale:'',

                submitButtonVisible: false,
                deleteButtonVisible: false,
                backButton: false
            }
        },
        computed: {
            actionsDropdownVisible() {
                return this.deleteButtonVisible;
            }
        },
        actions: {
            localChanged(newLocale) {
                this.locale=newLocale
            },
            setup(config) {
                let { locales, submitButtonVisible, deleteButtonVisible, backButton } = config;

                this.locales = locales;
                this.submitButtonVisible = submitButtonVisible;
                this.deleteButtonVisible = deleteButtonVisible;
                this.backButton = backButton;
            }
        }
    }
</script>