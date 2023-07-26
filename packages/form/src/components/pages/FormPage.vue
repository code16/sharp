<template>
    <div class="FormPage">
        <div class="container">
            <Form
                v-model:form="form"
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
    import Form from '../Form.vue';
    import ActionBarForm from '../ActionBar.vue';
    import {Button} from "@sharp/ui";

    export default {
        components: {
            Button,
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
                const label = form.breadcrumb?.items[form.breadcrumb.items.length - 1]?.documentTitleLabel;
                if(label) {
                    document.title = `${label}, ${document.title}`;
                }
            },
        }
    }
</script>
