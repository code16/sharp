<template>
    <div class="SharpActionView">
        <div class="container">
            <template v-if="showErrorPage">
                <h1>Erreur {{errorPageData.status}}</h1>
                <p>{{errorPageData.message}}</p>
            </template>
            <template v-else>
                <component v-if="barComp" :is="barComp"></component>
                <slot></slot>
                <sharp-modal v-for="(modal,id) in mainModalsData" :key="id"
                             v-bind="modal.props" @ok="modal.okCallback" @hidden="modal.hiddenCallback">
                    {{modal.text}}
                </sharp-modal>
            </template>
        </div>
    </div>
</template>

<script>
    import ActionBars, { NameAssociation as actionBarCompName } from './action-bar/index';
    import EventBus from './EventBus';

    import Modal from './Modal';
    import Vue from 'vue';
    import axios from 'axios';

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
                axiosInstance: axios.create()
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
                mainModalId: 0,
                showErrorPage: false,
                errorPageData: null
            }
        },
        computed: {
            barComp() {
                return actionBarCompName[this.context];
            },
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
        created() {
            let { actionsBus, axiosInstance } = this._provided;

            actionsBus.$on('showMainModal', this.showMainModal);
            axiosInstance.interceptors.response.use(c=>c, error=>{
                let { response: {status, data} } = error;
                if(status === 404) {
                    this.showErrorPage = true;
                    this.errorPageData = {
                        status, message: data.message
                    }
                }
                return Promise.reject(error);
            });
        }
    }
</script>