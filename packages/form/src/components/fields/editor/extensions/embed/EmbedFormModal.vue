<template>
    <Modal
        :visible="visible"
        :loading="loading"
        title=""
        @ok="handleSubmitButtonClicked"
    >
        <transition>
            <template v-if="visible">
                <Form
                    class="SharpCommandForm"
                    :entity-key="$form.entityKey"
                    :instance-id="$form.instanceId"
                    :form="form"
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
    import { Modal } from "sharp-ui";
    import { Form } from "../../../../index";

    export default {
        inject: {
            '$form': {
                default: null,
            }
        },
        components: {
            Modal,
            Form,
        },
        props: {
            visible: Boolean,
            form: Object,
        },
        data() {
            return {
                loading: false,
            }
        },
        methods: {
            handleLoadingChanged(loading) {
                this.loading = loading;
            },
            handleSubmitButtonClicked() {
                this.$refs.form.submit({

                });
            },
        }
    }
</script>
