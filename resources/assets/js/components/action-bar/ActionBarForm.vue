<template>
    <sharp-action-bar :ready="ready">
        <template slot="left">
            <button class="SharpButton SharpButton--secondary-accent" @click="emitAction('cancel')">
                {{ showBackButton ? label('back_button') : label('cancel_button') }}
            </button>

            <div v-if="showDeleteButton" class="w-100 h-100">
                <collapse transition-class="SharpButton__collapse-transition">
                    <template slot="frame-0" slot-scope="frame">
                        <button class="SharpButton SharpButton--danger" @click="frame.next(focusDelete)">
                            <svg  width='16' height='16' viewBox='0 0 16 24' fill-rule='evenodd'>
                                <path d='M4 0h8v2H4zM0 3v4h1v17h14V7h1V3H0zm13 18H3V8h10v13z'></path>
                                <path d='M5 10h2v9H5zm4 0h2v9H9z'></path>
                            </svg>
                        </button>
                    </template>
                    <template slot="frame-1" slot-scope="frame">
                        <button @click="emitAction('delete')" @blur="frame.next()" ref="openDelete" class="SharpButton SharpButton--danger">
                            {{ label('delete_button') }}
                        </button>
                    </template>
                </collapse>
            </div>
        </template>
        <template slot="right">
            <button v-if="showSubmitButton" class="SharpButton SharpButton--accent" @click="emitAction('submit')">
                {{ label('submit_button',opType) }}
            </button>
        </template>
    </sharp-action-bar>
</template>

<script>
    import SharpActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';

    import SharpLocaleSelector from '../LocaleSelector';
    import SharpDropdown from '../dropdown/Dropdown.vue';
    import SharpDropdownItem from '../dropdown/DropdownItem.vue';

    import Collapse from '../Collapse';

    import { ActionEvents } from '../../mixins';

    import { lang } from '../../mixins/Localization';

    export default {
        name: 'SharpActionBarForm',
        mixins: [ActionBarMixin, ActionEvents],
        components: {
            SharpActionBar,
            SharpLocaleSelector,
            SharpDropdown,
            SharpDropdownItem,
            Collapse
        },
        data() {
            return {
                showSubmitButton: false,
                showDeleteButton: false,
                showBackButton: false,

                deleteButtonOpened: false,

                opType: 'create', // or 'update'
                actionsState: null
            }
        },
        methods: {
            focusDelete() {
                if(this.$refs.openDelete) {
                    this.$refs.openDelete.focus();
                }
            },
            label(element, extra) {
                let localeKey = `action_bar.form.${element}`, stateLabel;
                if(this.actionsState) {
                    let { state, modifier } = this.actionsState;
                    stateLabel = lang(`${localeKey}.${state}.${modifier}`);
                }
                if(!stateLabel && extra) localeKey+=`.${extra}`;
                return stateLabel || lang(localeKey);
            }
        },
        actions: {
            setup(config) {
                let { showSubmitButton, showDeleteButton, showBackButton, opType } = config;

                this.showSubmitButton = showSubmitButton;
                this.showDeleteButton = showDeleteButton;
                this.showBackButton = showBackButton;
                this.opType = opType;
            },
            updateActionsState(actionsState) {
                this.actionsState = actionsState;
            }
        }
    }
</script>