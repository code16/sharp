<template>
    <div class="SharpFilterControl" @click="handleClicked">
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
    import DropdownArrow from '../dropdown/Arrow';

    export default {
        name: 'SharpFilterControl',
        props: {
            label: String,
            noCaret: Boolean,
        },
        components: {
            DropdownArrow,
        },
        data() {
            return {
                opened: false,
            }
        },
        methods: {
            handleClicked() {
                this.opened = !this.opened;
                this.$emit('click');
            },
        },
    }
</script>