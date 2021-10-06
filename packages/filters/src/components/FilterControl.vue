<template>
    <div class="input-group input-group-sm flex-nowrap SharpFilterControl" @click="handleClicked">
        <template v-if="label">
            <div class="input-group-text">
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
            opened: Boolean,
            label: String,
        },
        methods: {
            handleClicked() {
                this.$emit('click');
            },
        },
    }
</script>
