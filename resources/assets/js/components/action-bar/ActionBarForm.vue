<template>
    <sharp-action-bar :ready="ready">
        <template slot="left">
            <button class="SharpButton SharpButton--secondary-accent" @click="emitAction('cancel')">
                {{ showBackButton ? l('action_bar.form.back_button') : l('action_bar.form.cancel_button') }}
            </button>

            <div v-if="showDeleteButton" class="w-100 h-100">
                <collapse transition-class="SharpButton__collapse-transition">
                    <template slot="frame-0" scope="frame">
                        <button class="SharpButton SharpButton--danger" @click="frame.next(focusDelete)">
                            <svg  width='16' height='24' viewBox='0 0 16 24' fill-rule='evenodd'>
                                <path d='M4 0h8v2H4zM0 3v4h1v17h14V7h1V3H0zm13 18H3V8h10v13z'></path>
                                <path d='M5 10h2v9H5zm4 0h2v9H9z'></path>
                            </svg>
                        </button>
                    </template>
                    <template slot="frame-1" scope="frame">
                        <button @click="emitAction('delete')" @blur="frame.next()" ref="openDelete" class="SharpButton SharpButton--danger">
                            {{ l('action_bar.form.delete_button') }}
                        </button>
                    </template>
                </collapse>
            </div>

            <!--<sharp-locale-selector v-if="locales" @input="l=>emitAction('localeChanged',l)" :value="locale" :locales="locales"></sharp-locale-selector>-->
        </template>
        <template slot="right">
            <button v-if="showSubmitButton" class="SharpButton SharpButton--accent" @click="emitAction('submit')">
                {{ l(`action_bar.form.submit_button.${opType}`) }}
            </button>
        </template>
    </sharp-action-bar>
</template>

<script>
    import ActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';

    import LocaleSelector from '../LocaleSelector';
    import { Dropdown, DropdownItem } from '../dropdown';

    import Collapse from '../Collapse';

    import { Localization, ActionEvents } from '../../mixins';

    export default {
        name: 'SharpActionBarForm',
        mixins: [ActionBarMixin, ActionEvents, Localization],
        components: {
            [ActionBar.name]:ActionBar,
            [LocaleSelector.name]:LocaleSelector,
            [Dropdown.name]: Dropdown,
            [DropdownItem.name]: DropdownItem,
            Collapse
        },
        data() {
            return {
                locales:null,
                locale:'',

                showSubmitButton: false,
                showDeleteButton: false,
                showBackButton: false,

                deleteButtonOpened: false,

                opType: 'create', // or 'update'
            }
        },
        methods: {
            focusDelete() {
                if(this.$refs.openDelete) {
                    this.$refs.openDelete.focus();
                }
            }
        },
        actions: {
            localChanged(newLocale) {
                this.locale=newLocale
            },
            setup(config) {
                let { locales, showSubmitButton, showDeleteButton, showBackButton, opType } = config;

                this.locales = locales;
                this.showSubmitButton = showSubmitButton;
                this.showDeleteButton = showDeleteButton;
                this.showBackButton = showBackButton;
                this.opType = opType;
            }
        }
    }
</script>