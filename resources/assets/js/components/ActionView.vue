<template>
    <div class="SharpActionView">
        <template v-if="showErrorPage">
            <div class="container">
                <h1>Error {{errorPageData.status}}</h1>
                <p>{{errorPageData.message}}</p>
            </div>
        </template>
        <template v-else>
            <slot />
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
            <template v-for="modal in mainModals">
                <Modal
                    v-bind="modal.props"
                    @ok="modal.okCallback"
                    @hidden="modal.hiddenCallback"
                    :key="modal.id"
                >
                    {{ modal.text }}
                </Modal>
            </template>
        </template>

        <LoadingOverlay :visible="isLoading" />
    </div>
</template>

<script>
    import { api } from "../api";
    import { Modal, LoadingOverlay } from 'sharp-ui';
    import { mainModals } from "../util/modal";

    export default {
        name:'SharpActionView',
        components: {
            Modal,
            LoadingOverlay,
        },

        provide() {
            return {
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
                showErrorPage: false,
                errorPageData: null
            }
        },
        computed: {
            mainModals,
            isLoading() {
                return this.$store.getters.isLoading;
            },
        },
        created() {
            let { axiosInstance } = this._provided;

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
