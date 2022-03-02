<template>
    <TextField
        :class="{ 'hide-controls': !showControls }"
        :value="value"
        :min="min"
        :max="max"
        :step="step"
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
            value: Number,
            showControls: Boolean,
            min: Number,
            max: Number,
            step: Number,
        },
    }
</script>
