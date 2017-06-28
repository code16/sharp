<template>
    <sharp-action-bar>
        <template slot="left">
            <sharp-locale-selector v-if="locales" @input="l=>emitAction('locale-changed',l)" :value="locale" :locales="locales"></sharp-locale-selector>
        </template>
        <template slot="right">
            <button class="SharpButton SharpButton--primary" @click="emitAction('main-button-clicked')" :disabled="submitDisabled">Envoyer</button>
        </template>
    </sharp-action-bar>
</template>

<script>
    import ActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';

    import LocaleSelector from '../LocaleSelector';

    export default {
        name: 'SharpActionBarForm',
        mixins: [ActionBarMixin],
        components: {
            [ActionBar.name]:ActionBar,
            [LocaleSelector.name]:LocaleSelector
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