<template>
    <sharp-multiselect class="SharpLocaleSelector"
                       :options="locales"
                       :searchable="false"
                       :value="value"
                       @input="$emit('input',$event)">
    </sharp-multiselect>
</template>

<script>
    import SharpMultiselect from './Multiselect';

    export default {
        name: 'SharpLocaleSelector',
        components: {
            SharpMultiselect
        },
        props: {
            locales: Array,
            value: String
        },
        computed: {
        }
    }
</script>