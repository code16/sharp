<template>
    <Modal
        modal-class="form-modal"
        v-model:visible="visible"
        :loading="loading"
        :title="title"
        :ok-title="confirmLabel"
        @ok="handleSubmitButtonClicked"
        @hidden="handleClosed"
    >
        <transition>
            <template v-if="visible">
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
            entityKey: String,
            instanceId: [Number, String],
            loading: Boolean,
        },
        data() {
            return {
                visible: false,
            }
        },
        watch: {
            command(command) {
                this.visible = !!command?.form;
            }
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
