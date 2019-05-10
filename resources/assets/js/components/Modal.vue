<template>
    <b-modal class="SharpModal" :class="{ 'SharpModal--error': isError }"
        v-bind="$attrs"
        :title="title"
        :visible="visible"
        :cancel-title="cancelTitle || l('modals.cancel_button')"
        :ok-title="okTitle || l('modals.ok_button')"
        :ok-only="okOnly"
        no-enforce-focus
        v-on="$listeners"
        @show="handleShow"
        @hide="handleHide"
        ref="modal"
    >
        <template slot="modal-header">
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
    import Localization from '../mixins/Localization';
    import BModal from 'bootstrap-vue/es/components/modal/modal';

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
        },

        methods: {
            show() {
                this.$refs.modal.show();
            },
            hide() {
                this.$refs.modal.hide();
            },
            handleShow() {
                this.$emit('update:visible', true);
            },
            handleHide() {
                this.$emit('update:visible', false);
            },
        }
    }
</script>
