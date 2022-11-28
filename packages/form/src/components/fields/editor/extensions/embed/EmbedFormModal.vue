<template>
    <Modal
        modal-class="form-modal"
        :visible="visible"
        :loading="loading"
        v-on="$listeners"
        @ok="handleSubmitButtonClicked"
        @close="handleCancelClicked"
        @cancel="handleCancelClicked"
    >
        <template v-slot:title>
            <slot name="title"></slot>
        </template>
        <transition>
            <template v-if="visible">
                <Form
                    :entity-key="$form.entityKey"
                    :instance-id="$form.instanceId"
                    :form="form"
                    :show-alert="false"
                    independant
                    ignore-authorizations
                    no-tabs
                    style="transition-duration: 300ms"
                    ref="form"
                />
            </template>
        </transition>
    </Modal>
</template>

<script>
    import { Modal } from "sharp-ui";
    import Form from "../../../../Form";

    export default {
        components: {
            Modal,
            Form,
        },
        inject: {
            '$form': {
                default: null,
            }
        },
        props: {
            visible: Boolean,
            form: Object,
            post: Function,
        },
        data() {
            return {
                loading: false,
            }
        },
        methods: {
            handleSubmitButtonClicked(e) {
                e.preventDefault();
                this.loading = true;
                this.$refs.form.submit({ postFn:this.post })
                    .finally(() => {
                        this.loading = false;
                    });
            },
            handleCancelClicked() {
                this.$emit('cancel');
            },
        }
    }
</script>
