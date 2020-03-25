<template>
    <div class="FormPage">
        <ActionBarForm />
        <Form :entity-key="entityKey" :instance-id="instanceId" />
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