<template>
    <div class="SharpActionView">
        <div class="container">
            <component v-if="barComp" :is="barComp"></component>
            <slot></slot>
            <sharp-modal ref="mainModal" v-bind="mainModalProps">
                {{mainModalText}}
            </sharp-modal>
        </div>
    </div>
</template>

<script>
    import ActionBars, { NameAssociation as actionBarCompName } from './action-bar/index';
    import EventBus from './EventBus';

    import Modal from './Modal';
    import Vue from 'vue';

    export default {
        name:'SharpActionView',
        components: {
            [Modal.name]: Modal,
            ...ActionBars,
        },

        provide() {
            return {
                actionsBus: new EventBus({name:'SharpActionsEventBus'}),
            }
        },

        props: {
            context:{
                type:String,
                required:true
            }
        },

        data() {
            return {
                mainModalText:"",
                mainModalProps: {},

            }
        },
        computed: {
            barComp() {
                return actionBarCompName[this.context];
            }
        },
        methods: {
            showMainModal(props) {
                let {text, okCallback, okCloseOnly, isError, ...sharedProps} = props;

                this.mainModalText = text;
                this.$refs.mainModal.show();

                this.$refs.mainModal.$off('ok');
                if(okCallback) {
                    this.$refs.mainModal.$on('ok', okCallback);
                }

                this.mainModalProps = {
                    ...sharedProps,
                    okOnly:okCloseOnly,
                    noCloseOnBackdrop:okCloseOnly,
                    noCloseOnEsc:okCloseOnly,
                    isError
                }
            }
        },
        mounted() {
            this._provided.actionsBus.$on('showMainModal', this.showMainModal);
        }
    }
</script>