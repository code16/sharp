<template>
    <SharpModal :visible.sync="visible" @ok="handleSubmitButtonClicked" @hidden="handleClosed">
        <transition>
            <SharpForm
                v-if="visible"
                :props="form"
                independant
                ignore-authorizations
                style="transition-duration: 300ms"
                ref="form"
            />
        </transition>
    </SharpModal>
</template>

<script>
    import SharpModal from '../Modal.vue';
    import SharpForm from '../form/Form.vue';

    export default {
        components: {
            SharpModal,
            SharpForm,
        },
        props: {
            form: Object,
        },
        data() {
            return {
                visible: false
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
            }
        },
    }
</script>