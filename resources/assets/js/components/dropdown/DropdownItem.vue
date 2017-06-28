<template>
    <div><slot></slot></div>
</template>

<script>
    export default {
        name: 'SharpDropdownItem',
        computed: {
            text() {
                return this.$slots.default && this.$slots.default[0].text;
            }
        }
    }
</script>