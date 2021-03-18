<template>
    <div :class="{ 'card':root }">
        <div :class="{ 'card-body':root }">
            <div class="row gy-1 gx-3" :class="inline ? 'row-cols-auto' : 'row-cols-1'">
                <template v-for="(option, index) in options">
                    <div class="col">
                        <div class="SharpSelect__item" :key="option.id">
                            <input type="radio"
                                class="SharpRadio"
                                tabindex="0"
                                :id="`${uniqueIdentifier}${index}`"
                                :checked="isSelected(option)"
                                :value="option.id"
                                :disabled="readOnly"
                                :name="uniqueIdentifier"
                                @change="handleRadioChanged(option)"
                            >
                            <label class="SharpRadio__label" :for="`${uniqueIdentifier}${index}`">
                                <span class="SharpRadio__appearance"></span>
                                {{ labels[option.id] }}
                            </label>
                        </div>
                    </div>
                </template>
            </div>
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
