<template>
    <div class="SharpCheck">
        <label class="SharpCheck__label">
            <input class="SharpCheck__input" type="checkbox" @change="handleCheck" :checked="value" :disabled="readOnly">
            <span class="SharpCheck__appearance">
                <svg class="SharpCheck__checkmark" width="12" height="9" viewBox="0 0 12 9" fill-rule="evenodd">
                    <path d="M4.1 6.1L1.4 3.4 0 4.9 4.1 9l7.6-7.6L10.3 0z"></path>
                </svg>
            </span>
            <span class="SharpChek__label-text">{{text}}</span>
        </label>
    </div>
</template>

<script>
    export default {
        name: 'SharpCheck',
        props: {
            value:Boolean,
            text:String,
            readOnly: Boolean
        },
        methods: {
            handleCheck(e) {
                this.$emit('input', e.target.checked);
            }
        }
    }
</script>