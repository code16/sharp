<template>
    <component :is="tag" v-html="value" style="margin-top: .25em">
    </component>
</template>

<script>
    export default {
        props: {
            value: String,
            level: Number,
        },
        computed: {
            tag() {
                return `h${this.level}`;
            }
        },
    }
</script>
