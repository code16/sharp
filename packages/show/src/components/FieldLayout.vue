<template>
    <div>
        <template v-if="label">
            <div class="show-field__label form-label">{{ label }}</div>
        </template>
        <div class="show-field__content">
            <slot />
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            label: String,
        }
    }
</script>
