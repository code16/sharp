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
            <notifications position="top right" animation-name="slideRight" style="top:6rem; right: 1rem" reverse>
                <template slot="body" slot-scope="{ item, close }">
                    <div class="toast show mb-3" :class="`border-${item.type}`" role="alert" aria-live="assertive" aria-atomic="true" data-test="notification">
                        <div class="toast-header">
                            <strong class="me-auto">{{ item.title }}</strong>
                            <button type="button" class="btn-close" data-test="close-notification" aria-label="Close" @click="close"></button>
                        </div>
                        <template v-if="item.text">
                            <div class="toast-body" v-html="item.text">
                            </div>
                        </template>
                    </div>
                </template>
            </notifications>
            <template v-for="dialog in dialogs">
                <Modal
                    v-bind="dialog.props"
                    @ok="dialog.okCallback"
                    @hidden="dialog.hiddenCallback"
                    :key="dialog.id"
                >
                    {{ dialog.text }}
                </Modal>
            </template>
        </template>

        <LoadingOverlay :visible="isLoading" />
    </div>
</template>

<script>
    import { api } from "../api";
    import { Modal, LoadingOverlay } from 'sharp-ui';
    import { showNotification } from "../util/notifications";

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
            dialogs() {
                return this.$store.state.dialogs;
            },
            isLoading() {
                return this.$store.getters.isLoading;
            },
        },
        created() {
            let { axiosInstance } = this._provided;

            axiosInstance.interceptors.response.use(c=>c, error=>{
                let { response: {status, data}, config: { method } } = error;

                if(method==='get' && status === 404 || status === 403) {
                    this.showErrorPage = true;
                    this.errorPageData = {
                        status, message: data.message
                    }
                }
                return Promise.reject(error);
            });
        },
    }
</script>
