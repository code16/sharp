<template>
    <b-dropdown-item :active="active" @click="handleClick">
        <slot />
    </b-dropdown-item>
</template>

<script>
    import { BDropdownItem } from 'bootstrap-vue';

    export default {
        name: 'SharpDropdownItem',

        props: {
            active: Boolean,
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
