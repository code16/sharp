<template>
    <b-tooltip>
        <slot />
    </b-tooltip>
</template>

<script>
    import { BTooltip } from 'bootstrap-vue';

    export default {
        components: {
            BTooltip,
        },
        props: {

        },
    }
</script>
