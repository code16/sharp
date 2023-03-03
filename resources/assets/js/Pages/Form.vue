<template>
    <div class="FormPage">
        <div class="container">
            <Form :form="form">
                <template v-slot:action-bar="{ props, listeners }">
                    <ActionBarForm v-bind="props" v-on="listeners" />
                </template>
            </Form>
        </div>
    </div>
</template>

<script>
    import { Form } from "sharp-form";
    import ActionBarForm from 'sharp-form/src/components/ActionBar.vue';

    export default {
        components: {
            Form,
            ActionBarForm,
        },
        props: {
            entityKey: String,
            instanceId: String,
            form: Object,
        },
        methods: {
        }
    }
</script>
