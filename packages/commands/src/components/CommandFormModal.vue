<template>
    <Modal
        modal-class="form-modal"
        :visible="!!form"
        :loading="loading"
        :title="title"
        :ok-title="confirmLabel"
        @ok="handleSubmitButtonClicked"
        @hidden="handleClosed"
    >
        <transition>
            <template v-if="!!form">
                <Form
                    :entity-key="entityKey"
                    :instance-id="instanceId"
                    :form="command.form"
                    :show-alert="false"
                    independant
                    ignore-authorizations
                    @loading="handleLoadingChanged"
                    style="transition-duration: 300ms"
                    ref="form"
                />
            </template>
        </transition>
    </Modal>
</template>

<script>
    import { Modal, LoadingOverlay } from '@sharp/ui';
    import { Form } from '@sharp/form';

    export default {
        components: {
            Modal,
            Form,
            LoadingOverlay,
        },
        props: {
            command: Object,
            form: Object,
            entityKey: String,
            instanceId: [Number, String],
            loading: Boolean,
        },
        computed: {
            title() {
                return this.command?.modal_title ?? this.command?.label;
            },
            confirmLabel() {
                return this.command?.modal_confirm_label;
            },
        },
        methods: {
            submit(...args) {
                return this.$refs.form.submit(...args);
            },
            handleSubmitButtonClicked(e) {
                e.preventDefault();
                this.$emit('submit', this);
            },
            handleClosed() {
                this.$emit('close');
            },
            handleLoadingChanged(loading) {
                this.$emit('update:loading', loading);
            },
        },
    }
</script>
