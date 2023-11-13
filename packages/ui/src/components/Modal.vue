<template>
    <b-modal
        v-bind="$attrs"
        :title="title"
        :visible="visible"
        :ok-only="okOnly"
        :static="static"
        :modal-class="[modalClass, 'SharpModal']"
        :title-class="{ 'text-danger': isError }"
        :header-class="{ 'pb-0':!title }"
        :no-enforce-focus="noEnforceFocus"
        :no-close-on-backdrop="noCloseOnBackdrop"
        v-on="$listeners"
        @change="handleVisiblityChanged"
        ref="modal"
    >

        <template v-if="$slots.title" v-slot:modal-title>
            <slot name="title" />
        </template>

        <slot />

        <template v-slot:modal-footer="{ cancel, ok }">
            <div class="w-100">
                <div class="row">
                    <div class="col">
                        <slot name="footer-prepend" />
                    </div>
                    <div class="col-auto align-self-end">
                        <div class="row gx-2">
                            <template v-if="!okOnly">
                                <div class="col-auto">
                                    <button class="btn btn-outline-primary" @click="cancel">
                                        {{ cancelTitle || l('modals.cancel_button') }}
                                    </button>
                                </div>
                            </template>

                            <div class="col-auto">
                                <button
                                    class="btn position-relative"
                                    style="min-width: 70px"
                                    :class="okClasses"
                                    :disabled="loading"
                                    @click="ok"
                                >
                                    <span :class="{ 'invisible': loading }">
                                        {{ okTitle || l('modals.ok_button') }}
                                    </span>
                                    <template v-if="loading">
                                        <LoadingOverlay class="bg-transparent" absolute small />
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </b-modal>
</template>

<script>
    import { Localization } from 'sharp/mixins';
    import { BModal } from 'bootstrap-vue';
    import Loading from "./loading/Loading.vue";
    import LoadingOverlay from "./loading/LoadingOverlay.vue";

    export default {
        name: 'SharpModal',
        mixins: [Localization],

        components: {
            LoadingOverlay,
            Loading,
            BModal
        },
        inheritAttrs: false,

        props: {
            // bootstrap-vue override
            visible: Boolean,
            cancelTitle: String,
            title: String,
            okTitle: String,
            okOnly: Boolean,
            okVariant: {
                type: String,
                default: 'primary',
            },
            static: Boolean,
            modalClass: String,
            noCloseOnBackdrop: {
                type: Boolean,
                default: true,
            },
            noEnforceFocus: {
                type: Boolean,
                default: true,
            },
            // custom props
            isError: Boolean,
            loading: Boolean,
        },

        computed: {
            okClasses() {
                return {
                    'btn-lg': this.okOnly,
                    [`btn-${this.okVariant}`]: !!this.okVariant,
                }
            },
        },

        methods: {
            show() {
                this.$refs.modal.show();
            },
            hide() {
                this.$refs.modal.hide();
            },
            handleVisiblityChanged(visible) {
                this.$emit('update:visible', visible);
            },
        }
    }
</script>
