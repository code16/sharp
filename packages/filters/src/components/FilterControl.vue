<template>
    <div class="SharpFilterControl" :class="classes" @click="handleClicked">
        <span class="SharpFilterControl__text">
            {{ label }}
        </span>
        <slot />
        <template v-if="!noCaret">
            <span class="SharpFilterControl__caret-container">
                <DropdownArrow class="SharpFilterControl__caret" />
            </span>
        </template>
    </div>
</template>

<script>
    import { DropdownArrow } from 'sharp/components';

    export default {
        name: 'SharpFilterControl',
        props: {
            opened: Boolean,
            label: String,
            noCaret: Boolean,
        },
        components: {
            DropdownArrow,
        },
        computed: {
            classes() {
                return {
                    'SharpFilterControl--open': this.opened,
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