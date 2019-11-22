<template>
    <div class="ShowTextField">
        <template v-if="label">
            <span class="text-uppercase"></span>{{ label }}
        </template>
        <span v-html="value"></span>
    </div>
</template>

<script>
    export default {
        props: {
            value: String,
            collapseToWordCount: Number,
            label: String,
        }
    }
</script>