<template>
    <b-modal v-bind="$attrs"
        :title="title"
        :visible="visible"
        :cancel-title="cancelTitle || l('modals.cancel_button')"
        :ok-title="okTitle || l('modals.ok_button')"
        :ok-only="okOnly"
        :static="static"
        :modal-class="['SharpModal', {'SharpModal--error': isError}]"
        no-enforce-focus
        v-on="$listeners"
        @change="handleVisiblityChanged"
        ref="modal"
    >
        <template v-slot:modal-header>
            <div>
                <h5 class="SharpModal__heading">
                    <slot name="title">{{ title }}</slot>
                </h5>
                <button v-if="!okOnly" class="SharpModal__close" type="button" @click="hide">
                    <svg class="SharpModal__close-icon" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                        <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                    </svg>
                </button>
            </div>
        </template>
        <slot />
    </b-modal>
</template>

<script>
    import { Localization } from 'sharp/mixins';
    import { BModal } from 'bootstrap-vue';

    export default {
        name: 'SharpModal',
        mixins: [Localization],

        components: {
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

            // custom props
            isError: Boolean,
            static: Boolean,
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
