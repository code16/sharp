<template>
    <b-dropdown-item :active="active" :disabled="disabled" @click="handleClick">
        <slot />
    </b-dropdown-item>
</template>

<script>
    import { BDropdownItem } from 'bootstrap-vue';

    export default {
        name: 'SharpDropdownItem',

        props: {
            active: Boolean,
            disabled: Boolean,
        },

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
