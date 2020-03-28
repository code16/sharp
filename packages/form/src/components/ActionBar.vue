<template>
    <ActionBar :ready="ready" container>
        <template v-slot:left>
            <button class="SharpButton SharpButton--secondary-accent" @click="emitAction('cancel')">
                {{ showBackButton ? label('back_button') : label('cancel_button') }}
            </button>

            <div v-if="showDeleteButton" class="w-100 h-100">
                <Collapse transition-class="SharpButton__collapse-transition">
                    <template v-slot:frame-0="{ next }">
                        <button class="SharpButton SharpButton--danger" @click="next(focusDelete)">
                            <svg  width='16' height='16' viewBox='0 0 16 24' fill-rule='evenodd'>
                                <path d='M4 0h8v2H4zM0 3v4h1v17h14V7h1V3H0zm13 18H3V8h10v13z'></path>
                                <path d='M5 10h2v9H5zm4 0h2v9H9z'></path>
                            </svg>
                        </button>
                    </template>
                    <template v-slot:frame-1="{ next }">
                        <button @click="emitAction('delete')" @blur="next()" ref="openDelete" class="SharpButton SharpButton--danger">
                            {{ label('delete_button') }}
                        </button>
                    </template>
                </Collapse>
            </div>
        </template>
        <template v-slot:right>
            <button v-if="showSubmitButton" class="SharpButton SharpButton--accent" @click="emitAction('submit')">
                {{ label('submit_button',opType) }}
            </button>
        </template>
    </ActionBar>
</template>

<script>
    import { lang } from 'sharp';
    import { ActionBar, Collapse } from 'sharp-ui';
    import { ActionEvents } from 'sharp/mixins';


    export default {
        name: 'SharpActionBarForm',
        mixins: [ActionEvents],
        components: {
            ActionBar,
            Collapse
        },
        data() {
            return {
                ready: false,

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
            },
            emitAction() {
                this.actionsBus.$emit(...arguments);
            }
        },
        mounted() {
            this.actionsBus.$on('setup', () => {
                this.ready = true;
            });
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