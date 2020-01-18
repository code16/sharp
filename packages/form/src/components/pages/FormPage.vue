<template>
    <div class="FormPage">
        <Form :entity-key="entityKey" :instance-id="instanceId" />
    </div>
</template>

<script>
    import Form from '../Form';

    export default {
        components: {
            Form,
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