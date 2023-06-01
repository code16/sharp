<template>
    <div class="mb-3">
        <div class="row gx-3">
            <div class="col">
                <template v-if="showBreadcrumb">
                    <Breadcrumb :items="breadcrumb" />
                </template>
            </div>
            <template v-if="showDeleteButton">
                <div class="col-auto">
                    <template v-if="deleteFocused">
                        <Button variant="danger" :disabled="loading" small @click="handleDeleteClicked" @blur="deleteFocused = false">
                            {{ l('action_bar.form.delete_button') }}
                        </Button>
                    </template>
                    <template v-else>
                        <Button variant="danger" outline :disabled="loading" small @click="handleDeleteClicked">
                            {{ l('action_bar.form.delete_button') }}
                        </Button>
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { lang } from 'sharp';
    import { Button, Breadcrumb } from 'sharp-ui';
    import { Localization } from "sharp/mixins";

    export default {
        name: 'SharpActionBarForm',
        mixins: [Localization],
        components: {
            Button,
            Breadcrumb,
        },
        props: {
            showSubmitButton: Boolean,
            showDeleteButton: Boolean,
            showBackButton: Boolean,
            create: Boolean,
            uploading: Boolean,
            loading: Boolean,
            breadcrumb: Array,
            showBreadcrumb: Boolean,
            hasDeleteConfirmation: Boolean,
        },
        data() {
            return {
                deleteFocused: false,
            }
        },
        computed: {
            submitLabel() {
                if(this.uploading) {
                    return lang('action_bar.form.submit_button.pending.upload')
                }
                return this.create
                    ? lang('action_bar.form.submit_button.create')
                    : lang('action_bar.form.submit_button.update');
            },
        },
        methods: {
            handleSubmitClicked() {
                this.$emit('submit');
            },
            handleDeleteClicked() {
                if(this.deleteFocused || this.hasDeleteConfirmation) {
                    this.$emit('delete');
                } else {
                    this.deleteFocused = true;
                }
            },
            handleCancelClicked() {
                this.$emit('cancel');
            },
        },
    }
</script>
