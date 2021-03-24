<template>
    <div class="input-group input-group-sm flex-nowrap SharpFilterControl" @click="handleClicked">
        <template v-if="label">
            <div class="input-group-text bg-white">
                {{ label }}
            </div>
        </template>

        <slot />
    </div>
</template>

<script>
    import { DropdownArrow } from 'sharp-ui';

    export default {
        name: 'SharpFilterControl',
        props: {
            opened: Boolean,
            label: String,
        },
        components: {
            DropdownArrow,
        },
        computed: {
            classes() {
                return {
                    // 'SharpFilterControl--open': this.opened,
                    // 'dropdown-toggle-no-caret': this.noCaret,
                }
            },
        },
        methods: {
            handleClicked() {
                this.$emit('click');
            },
        },
    }
</script>
