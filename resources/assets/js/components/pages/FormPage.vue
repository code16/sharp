<template>
    <div class="FormPage">
        <SharpForm :entity-key="entityKey" :instance-id="instanceId" />
    </div>
</template>

<script>
    import SharpForm from '../form/Form';

    export default {
        components: {
            SharpForm,
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