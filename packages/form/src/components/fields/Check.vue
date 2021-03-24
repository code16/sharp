<template>
    <div class="SharpCheck ">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" :id="resolvedId" :checked="value" :disabled="readOnly" @change="handleCheck">
            <label class="form-check-label" :for="resolvedId">
                {{ text }}
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'SharpCheck',
        props: {
            value: Boolean,
            text: String,
            readOnly: Boolean,
            uniqueIdentifier: String,
            id: String,
        },
        computed: {
            resolvedId() {
                return this.id ?? this.uniqueIdentifier;
            }
        },
        methods: {
            handleCheck(e) {
                this.$emit('input', e.target.checked);
            }
        }
    }
</script>
