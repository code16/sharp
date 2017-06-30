<template>
    <sharp-action-bar>
        <template slot="left">
            <sharp-locale-selector v-if="locales" @input="l=>emitAction('locale-changed',l)" :value="locale" :locales="locales"></sharp-locale-selector>
        </template>
        <template slot="right">
            <button class="SharpButton SharpButton--secondary" @click="emitAction('cancel')">{{ l('action_bar.form.cancel_button') }}</button>
            <button class="SharpButton SharpButton--primary" @click="emitAction('submit')" :disabled="submitDisabled">{{ l('action_bar.form.submit_button') }}</button>
            <sharp-dropdown class="SharpActionBar__actions-dropdown">
                <sharp-dropdown-item @click="emitAction('delete')">{{ l('action_bar.form.delete_button') }}</sharp-dropdown-item>
            </sharp-dropdown>
        </template>
    </sharp-action-bar>
</template>

<script>
    import ActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';

    import LocaleSelector from '../LocaleSelector';
    import { Dropdown, DropdownItem } from '../dropdown';

    import { Localization } from '../../mixins';

    export default {
        name: 'SharpActionBarForm',
        mixins: [ActionBarMixin, Localization],
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

                submitDisabled: false
            }
        },
        created() {
            this.actionsBus.$on('setup-locales',locales=>this.locales=locales);
            this.actionsBus.$on('locale-changed',newLocale=>this.locale=newLocale);

            this.actionsBus.$on('enable-submit',_=>this.submitDisabled=false);
            this.actionsBus.$on('disable-submit',_=>this.submitDisabled=true);
        }
    }
</script>