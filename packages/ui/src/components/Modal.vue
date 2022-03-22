<template>
    <b-modal
        v-bind="$attrs"
        :title="title"
        :visible="visible"
        :ok-only="okOnly"
        :static="static"
        modal-class="SharpModal"
        :title-class="{ 'text-danger': isError }"
        :header-class="{ 'pb-0':!title }"
        no-enforce-focus
        no-close-on-backdrop
        v-on="$listeners"
        @change="handleVisiblityChanged"
        ref="modal"
    >

        <template v-if="$slots.title" v-slot:modal-title>
            <slot name="title" />
        </template>

        <slot />

        <template v-slot:modal-footer="{ cancel, ok }">

            <template v-if="!okOnly">
                <button class="btn btn-outline-primary" @click="cancel">
                    {{ cancelTitle || l('modals.cancel_button') }}
                </button>
            </template>

            <button class="btn position-relative" :class="okClasses" :disabled="loading" @click="ok">
                <span :class="{ 'invisible': loading }">
                    {{ okTitle || l('modals.ok_button') }}
                </span>
                <template v-if="loading">
                    <LoadingOverlay class="bg-transparent" absolute small />
                </template>
            </button>
        </template>
    </b-modal>
</template>

<script>
    import { Localization } from 'sharp/mixins';
    import { BModal } from 'bootstrap-vue';
    import Loading from "./loading/Loading";
    import LoadingOverlay from "./loading/LoadingOverlay";

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
