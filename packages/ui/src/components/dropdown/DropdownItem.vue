<template>
    <b-dropdown-item @click="handleClick">
        <slot />
    </b-dropdown-item>
</template>

<script>
    import { BDropdownItem } from 'bootstrap-vue';

    export default {
        name: 'SharpDropdownItem',

        components: {
            BDropdownItem,
        },

        methods: {
            handleClick(e) {
                this.$emit('click', e);
            }
        }
    }
</script>
