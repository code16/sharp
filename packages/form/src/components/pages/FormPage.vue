<template>
    <div class="FormPage">
        <div class="container">
            <Form :entity-key="entityKey" :instance-id="instanceId" :live-update="liveUpdate">
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

    export default {
        components: {
            Form,
            ActionBarForm,
        },
        props: {
            liveUpdate: Boolean,
        },
        computed: {
            entityKey() {
                return this.$route.params.entityKey;
            },
            instanceId() {
                return this.$route.params.instanceId;
            }
        },
    }
</script>
