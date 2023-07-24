<template>
    <div class="SharpActionView">
        <TopBar>
            <template v-slot:right>
                <div class="row align-items-center gx-4">
                    <template v-if="$slots.search">
                        <div class="col-auto">
                            <slot name="search" />
                        </div>
                    </template>
                    <div class="col-auto">
                        <slot name="user-dropdown" />
                    </div>
                </div>
            </template>
        </TopBar>

        <template v-if="showErrorPage">
            <div class="container">
                <h1>Error {{errorPageData.status}}</h1>
                <p>{{errorPageData.message}}</p>
            </div>
        </template>
        <template v-else>

            <template v-if="$slots.default">
                <slot />
            </template>
            <template v-else>
                <router-view @error="handlePageError" />
            </template>

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
    import { Modal, LoadingOverlay, TopBar } from 'sharp-ui';

    export default {
        name:'SharpActionView',
        components: {
            Modal,
            LoadingOverlay,
            TopBar,
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
