<template>
    <Modal
        :visible.sync="visible"
        :loading="loading"
        @ok="handleSubmitButtonClicked"
        @hidden="handleClosed"
    >
        <transition>
            <Form
                v-if="visible"
                :props="form"
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
            form: Object,
        },
        data() {
            return {
                visible: false,
                loading: false,
            }
        },
        watch: {
            form(form) {
                this.visible = !!form;
            }
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
