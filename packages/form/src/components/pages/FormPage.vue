<template>
    <div class="FormPage">
        <div class="container">
            <Form
                :form.sync="form"
                :entity-key="entityKey"
                :instance-id="instanceId"
                @update:form="handleFormUpdated"
                @error="handleError"
            >
                <template v-slot:action-bar="{ props, listeners }">
                    <ActionBarForm v-bind="props" v-on="listeners" />
                </template>
            </Form>
        </div>
    </div>
</template>

<script>
    import Form from '../Form';
    import ActionBarForm from '../ActionBar';
    import { lang } from "sharp";

    export default {
        components: {
            Form,
            ActionBarForm,
        },
        data() {
            return {
                form: null,
            }
        },
        computed: {
            entityKey() {
                return this.$route.params.entityKey;
            },
            instanceId() {
                return this.$route.params.instanceId;
            }
        },
        methods: {
            handleError(error) {
                this.$emit('error', error);
            },
            handleFormUpdated(form) {
                this.updateDocumentTitle(form);
            },
            updateDocumentTitle(form) {
                const breadcrumbLabel = form.breadcrumb?.items[form.breadcrumb.items.length - 2]?.name;
                if(breadcrumbLabel) {
                    const title = lang('form.document_title').replace(':breadcrumb_label', breadcrumbLabel);
                    document.title = `${title}, ${document.title}`;
                }
            },
        }
    }
</script>
