<template>
    <Modal
        :visible.sync="visible"
        :loading="loading"
        :title="title"
        @ok="handleSubmitButtonClicked"
        @hidden="handleClosed"
    >
        <transition>
            <Form
                v-if="visible"
                :form="command.form"
                :show-alert="false"
                independant
                ignore-authorizations
                @loading="handleLoadingChanged"
                style="transition-duration: 300ms"
                ref="form"
            />
        </transition>
    </Modal>
</template>

<script>
    import { Modal, LoadingOverlay } from 'sharp-ui';
    import { Form } from 'sharp-form';

    export default {
        components: {
            Modal,
            Form,
            LoadingOverlay,
        },
        props: {
            command: Object,
        },
        data() {
            return {
                visible: false,
                loading: false,
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
        },
        methods: {
            submit(...args) {
                return this.$refs.form.submit(...args);
            },
            handleSubmitButtonClicked(e) {
                e.preventDefault();
                this.$emit('submit');
            },
            handleClosed() {
                this.$emit('close');
            },
            handleLoadingChanged(loading) {
                this.loading = loading;
            }
        },
    }
</script>
