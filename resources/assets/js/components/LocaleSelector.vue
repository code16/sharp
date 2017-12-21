<template>
    <sharp-multiselect class="SharpLocaleSelector"
                       :options="locales"
                       :searchable="false"
                       :allow-empty="false"
                       :value="value"
                       @input="$emit('input',$event)">
    </sharp-multiselect>
</template>

<script>
    import Multiselect from './Multiselect';

    export default {
        name: 'SharpLocaleSelector',
        components: {
            [Multiselect.name]: Multiselect
        },
        props: {
            locales: Array,
            value: String
        },
        computed: {
        }
    }
</script>