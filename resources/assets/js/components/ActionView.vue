<template>
    <div class="SharpActionView">
        <div class="container">
            <component v-if="barComp" :is="barComp"></component>
            <slot></slot>
            <sharp-modal v-for="(modal,id) in mainModalsData" :key="id"
                         v-bind="modal.props" @ok="modal.okCallback" @hidden="modal.hiddenCallback">
                {{modal.text}}
            </sharp-modal>
        </div>
    </div>
</template>

<script>
    import ActionBars, { NameAssociation as actionBarCompName } from './action-bar/index';
    import EventBus from './EventBus';

    import Modal from './Modal';
    import Vue from 'vue';

    const noop=()=>{};

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
                mainModalsData: {},
                mainModalId: 0
            }
        },
        computed: {
            barComp() {
                return actionBarCompName[this.context];
            }
        },
        methods: {
            showMainModal({text, okCallback=noop, okCloseOnly, isError, ...sharedProps}) {
                const curId = this.mainModalId;
                const hiddenCallback = _=>this.$delete(this.mainModalsData, curId);

                this.$set(this.mainModalsData,curId,{
                    props: {
                        ...sharedProps,
                        okOnly:okCloseOnly,
                        noCloseOnBackdrop:okCloseOnly,
                        noCloseOnEsc:okCloseOnly,
                        visible: true,
                        isError
                    },
                    okCallback, hiddenCallback,
                    text,
                });
                this.mainModalId++;
            }
        },
        mounted() {
            this._provided.actionsBus.$on('showMainModal', this.showMainModal);
        }
    }
</script>