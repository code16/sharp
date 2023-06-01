<template>
    <div class="SharpActionView">
        <template v-if="showErrorPage">
            <div class="container">
                <ActionBar />
                <h1>Error {{errorPageData.status}}</h1>
                <p>{{errorPageData.message}}</p>
            </div>
        </template>
        <template v-else>
            <ActionBar>
                <template v-slot:right>
                    <slot name="user-dropdown" />
                </template>
            </ActionBar>

            <router-view @error="handlePageError" />

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
                    <div v-html="dialog.text"></div>
                </Modal>
            </template>
        </template>

        <LoadingOverlay
            class="SharpActionView__loading"
            :visible="isLoading"
            fade
        />
    </div>
</template>

<script>
    import { createApi } from "../api";
    import { Modal, LoadingOverlay, ActionBar } from 'sharp-ui';

    export default {
        name:'SharpActionView',
        components: {
            Modal,
            LoadingOverlay,
            ActionBar
        },

        provide() {
            return {
                axiosInstance: createApi(),
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
        methods: {
            handlePageError(error) {
                const { response: { status, data }, config: { method } } = error;

                if(method === 'get' && status === 404 || status === 403) {
                    this.showErrorPage = true;
                    this.errorPageData = {
                        status, message: data.message
                    }
                }
            },
        },
    }
</script>
