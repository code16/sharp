<template>
    <div class="SharpActionView">
        <template v-if="!showErrorPage">
            <component v-if="barComp" :is="barComp"></component>
        </template>
        <div class="container">
            <template v-if="showErrorPage">
                <h1>Error {{errorPageData.status}}</h1>
                <p>{{errorPageData.message}}</p>
            </template>
            <template v-else>
                <slot></slot>
                <notifications position="top right" animation-name="slideRight" style="top:6rem" reverse>
                    <template slot="body" slot-scope="{ item, close }">
                        <div class="SharpToastNotification" :class="`SharpToastNotification--${item.type||'info'}`" role="alert" data-test="notification">
                            <div class="SharpToastNotification__details">
                                <h3 class="SharpToastNotification__title mb-2">{{ item.title }}</h3>
                                <p v-if="!!item.text" class="SharpToastNotification__caption" v-html="item.text"></p>
                            </div>
                            <button class="SharpToastNotification__close-button" type="button" @click="close" data-test="close-notification">
                                <svg class="SharpToastNotification__icon" aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                                    <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                                </svg>
                            </button>
                        </div>
                    </template>
                </notifications>
                <sharp-modal v-for="(modal,id) in mainModalsData" :key="id"
                    v-bind="modal.props" @ok="modal.okCallback" @hidden="modal.hiddenCallback">
                    {{modal.text}}
                </sharp-modal>
            </template>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import { actionBarByContext } from './action-bar';
    import EventBus from './EventBus';
    import { api } from "../api";
    import SharpModal from './Modal';

    const noop=()=>{};

    export default {
        name:'SharpActionView',
        components: {
            SharpModal
        },

        provide() {
            return {
                actionsBus: new EventBus({name:'SharpActionsEventBus'}),
                axiosInstance: api
            }
        },

        props: {
            context: {
                type: String,
                required: true
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
                return actionBarByContext(this.context);
            },
        },
        methods: {
            showMainModal({ text, okCallback=noop, okCloseOnly, isError, ...sharedProps }) {
                const curId = this.mainModalId;
                const hiddenCallback = () => this.$delete(this.mainModalsData, curId);

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
                let { response: {status, data}, config: { method } } = error;

                if(method==='get' && status === 404) {
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
