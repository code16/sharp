<template>
    <div class="SharpSearch" role="search">
        <form class="h-100" @submit.prevent="handleSubmitted">
            <label id="ab-search-label" class="SharpSearch__label visually-hidden" for="ab-search-input">{{ placeholder }}</label>
            <div class="position-relative">
                <svg class="SharpSearch__magnifier" width="16" height="16" viewBox="0 0 16 16" fill-rule="evenodd">
                    <path d="M6 2c2.2 0 4 1.8 4 4s-1.8 4-4 4-4-1.8-4-4 1.8-4 4-4zm0-2C2.7 0 0 2.7 0 6s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zM16 13.8L13.8 16l-3.6-3.6 2.2-2.2z"></path>
                    <path d="M16 13.8L13.8 16l-3.6-3.6 2.2-2.2z"></path>
                </svg>
                <input class="SharpSearch__input form-control form-control-sm"
                    :value="value"
                    :placeholder="placeholder"
                    type="text"
                    id="ab-search-input"
                    role="search"
                    aria-labelledby="ab-search-label"
                    @input="handleInput"
                    @focus="handleFocused"
                    @blur="handleBlur"
                    ref="input"
                >
                <button class="btn btn-sm SharpSearch__close text-muted h-100 d-inline-flex align-items-center"
                    :class="{ 'invisible': !clearVisible }"
                    type="button"
                    aria-label="Clear search"
                    @click="handleClearButtonClicked"
                >
                    <svg width="1.25em" height="1.25em" viewBox="0 0 16 16" fill-rule="evenodd">
                        <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: {
            value: String,
            placeholder: String,
            active: Boolean,
        },
        data() {
            return {
                localActive: !!this.active,
            }
        },
        watch: {
            active(active) {
                this.localActive = active;
            },
        },
        computed: {
            clearVisible() {
                return this.value && this.value.length > 0;
            },
        },
        methods: {
            handleClearButtonClicked() {
                this.$emit('clear');
                this.$emit('input', '');
                this.$refs.input.focus();
            },
            handleInput(e) {
                this.$emit('input', e.target.value);
            },
            handleFocused() {
                this.localActive = true;
                this.$emit('focus');
                this.$emit('update:active', true);
            },
            handleBlur() {
                this.localActive = false;
                this.$emit('blur');
                this.$emit('update:active', false);
            },
            handleSubmitted() {
                this.$emit('submit');
            },
        },
    }
</script>
