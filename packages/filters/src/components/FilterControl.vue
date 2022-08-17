<template>
    <div class="input-group input-group-sm flex-nowrap SharpFilterControl" @click="handleClicked">
        <template v-if="label">
            <div class="input-group-text" @click="handleLabelClicked">
                {{ label }}
            </div>
        </template>

        <slot />
    </div>
</template>

<script>
    export default {
        name: 'SharpFilterControl',
        props: {
            label: String,
        },
        methods: {
            handleLabelClicked() {
                this.$emit('label-click');
            },
            handleClicked() {
                this.$emit('click');
            },
        },
    }
</script>
