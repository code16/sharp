<template>
    <TextField
        :class="{ 'hide-controls': !showControls }"
        :value="value"
        :step="step"
        :min="min"
        :max="max"
        input-type="number"
        v-bind="$attrs"
        v-on="$listeners"
    />
</template>

<script>
    import TextField from './Text';

    export default {
        components: {
            TextField,
        },
        props: {
            value: [String, Number],
            showControls: Boolean,
            min: Number,
            max: Number,
            step: Number,
        },
    }
</script>
