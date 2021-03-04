<template>
    <ActionBar>
        <template v-slot:left>
            <Button variant="light" outline @click="handleCancelClicked">
                <template v-if="showBackButton">
                    {{ l('action_bar.form.back_button') }}
                </template>
                <template v-else>
                    {{ l('action_bar.form.cancel_button') }}
                </template>
            </Button>

            <template v-if="showDeleteButton">
                <div class="w-100 h-100 ml-3">
                    <Collapse>
                        <template v-slot:frame-0="{ next }">
                            <button class="SharpButton SharpButton--danger" @click="next(focusDelete)">
                                <svg  width='16' height='16' viewBox='0 0 16 24' fill-rule='evenodd'>
                                    <path d='M4 0h8v2H4zM0 3v4h1v17h14V7h1V3H0zm13 18H3V8h10v13z'></path>
                                    <path d='M5 10h2v9H5zm4 0h2v9H9z'></path>
                                </svg>
                            </button>
                        </template>
                        <template v-slot:frame-1="{ next }">
                            <Button variant="danger" @click="handleDeleteClicked" @blur="next()" ref="openDelete">
                                {{ l('action_bar.form.delete_button') }}
                            </Button>
                        </template>
                    </Collapse>
                </div>
            </template>
        </template>
        <template v-slot:right>
            <template v-if="showSubmitButton">
                <Button variant="light" :disabled="uploading" @click="handleSubmitClicked">
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
    import { ActionBar, Collapse, Button, Breadcrumb} from 'sharp-ui';
    import { Localization } from "sharp/mixins";

    export default {
        name: 'SharpActionBarForm',
        mixins: [Localization],
        components: {
            ActionBar,
            Collapse,
            Button,
            Breadcrumb,
        },
        props: {
            showSubmitButton: Boolean,
            showDeleteButton: Boolean,
            showBackButton: Boolean,
            create: Boolean,
            uploading: Boolean,
            breadcrumb: Array,
            showBreadcrumb: Boolean,
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
            focusDelete() {
                if(this.$refs.openDelete) {
                    this.$refs.openDelete.focus();
                }
            },
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
