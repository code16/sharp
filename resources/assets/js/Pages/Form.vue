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
                    <ActionBar v-bind="props" v-on="listeners" />
                </template>
            </Form>
        </div>
    </div>
</template>

<script>
    import { Form } from "sharp-form";
    import ActionBar from 'sharp-form/src/components/ActionBar.vue';

    export default {
        props: {
            entityKey: String,
            instanceId: String,
        },
        components: {
            Form,
            ActionBar,
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
        }
    }
</script>
