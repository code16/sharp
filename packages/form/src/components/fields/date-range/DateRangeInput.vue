<template>
    <input
        class="form-control"
        :value="value"
        :placeholder="placeholder"
        :disabled="readOnly"
        autocomplete="off"
        v-on="$listeners"
    >
</template>

<script>
    import { ClearButton } from "sharp-ui";

    export default {
        inheritAttrs: false,
        components: {
            ClearButton,
        },
        props: {
            value: String,
            placeholder: String,
            readOnly: Boolean,
        },
    }
</script>
