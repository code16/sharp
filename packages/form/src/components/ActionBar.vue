<template>
    <ActionBar>
        <template v-slot:left>
            <div class="row gx-3">
                <div class="col-auto">
                    <Button variant="light" large outline @click="handleCancelClicked">
                        <template v-if="showBackButton">
                            {{ l('action_bar.form.back_button') }}
                        </template>
                        <template v-else>
                            {{ l('action_bar.form.cancel_button') }}
                        </template>
                    </Button>
                </div>
                <template v-if="showDeleteButton">
                    <div class="col-auto">
                        <template v-if="deleteFocused">
                            <Button variant="danger" :disabled="loading" large @click="handleDeleteClicked" @blur="deleteFocused = false">
                                {{ l('action_bar.form.delete_button') }}
                            </Button>
                        </template>
                        <template v-else>
                            <Button class="px-3" variant="light" large outline @click="deleteFocused = true">
                                <svg width="1.125em" height="1.125em" viewBox="0 0 16 24" fill-rule="evenodd">
                                    <path d="M4 0h8v2H4zM0 3v4h1v17h14V7h1V3H0zm13 18H3V8h10v13z"></path>
                                    <path d="M5 10h2v9H5zm4 0h2v9H9z"></path>
                                </svg>
                            </Button>
                        </template>
                    </div>
                </template>
            </div>
        </template>
        <template v-slot:right>
            <template v-if="showSubmitButton">
                <Button variant="light" large :disabled="uploading || loading" @click="handleSubmitClicked">
                    {{ submitLabel }}
                </Button>
            </template>
        </template>
        <template v-slot:extras>
            <template v-if="showBreadcrumb">
                <Breadcrumb :items="breadcrumb" />
            </template>
        </template>
    </ActionBar>
</template>

<script>
    import { lang } from 'sharp';
    import { ActionBar, Button, Breadcrumb } from 'sharp-ui';
    import { Localization } from "sharp/mixins";

    export default {
        name: 'SharpActionBarForm',
        mixins: [Localization],
        components: {
            ActionBar,
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
                this.$emit('delete');
            },
            handleCancelClicked() {
                this.$emit('cancel');
            },
        },
    }
</script>
