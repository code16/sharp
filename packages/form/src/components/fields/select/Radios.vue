<template>
    <div :class="{ 'card card-body form-control':root }">
        <div class="row gy-1 gx-3" :class="inline ? 'row-cols-auto' : 'row-cols-1'">
            <template v-for="(option, index) in options">
                <div class="col" :key="option.id">
                    <div class="form-check mb-0">
                        <input type="radio"
                            class="form-check-input"
                            tabindex="0"
                            :id="`${uniqueIdentifier}.${index}`"
                            :checked="isSelected(option)"
                            :value="option.id"
                            :disabled="readOnly"
                            :name="uniqueIdentifier"
                            @change="handleRadioChanged(option)"
                        >
                        <label class="form-check-label" :for="`${uniqueIdentifier}.${index}`">
                            {{ labels[option.id] }}
                        </label>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { isSelected } from "../../../util/select";

    export default {
        props: {
            value: [String, Number],
            options: Array,
            labels: Object,
            uniqueIdentifier: String,
            readOnly: Boolean,
            inline: Boolean,
            root: Boolean,
        },
        methods: {
            isSelected(option) {
                return isSelected(option, this.value);
            },
            handleRadioChanged(option) {
                this.$emit('input', option.id);
            },
        },
    }
</script>
