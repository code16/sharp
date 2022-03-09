<template>
    <div class="SharpSearch" role="search">
        <form class="h-100" @submit.prevent="handleSubmitted">
            <label id="ab-search-label" class="SharpSearch__label visually-hidden" for="ab-search-input">{{ placeholder }}</label>
            <div class="input-group input-group-sm flex-nowrap">
                <div class="position-relative flex-fill" style="min-width: 0">
                    <input class="SharpSearch__input form-control form-control-sm"
                        v-model="search"
                        :placeholder="placeholder"
                        :disabled="disabled"
                        type="text"
                        id="ab-search-input"
                        role="search"
                        aria-labelledby="ab-search-label"
                        @focus="handleFocused"
                        @blur="handleBlur"
                        @focusout="handleFocusout"
                        ref="input"
                    >
                    <button class="btn btn-sm SharpSearch__clear h-100 d-inline-flex align-items-center position-absolute"
                        :class="{ 'invisible': !clearVisible }"
                        type="button"
                        aria-label="Clear search"
                        @click="handleClearButtonClicked"
                    >
                        <svg width="1em" height="1em" viewBox="0 0 16 16" fill-rule="evenodd">
                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z"></path>
                        </svg>
                    </button>
                </div>

                <button class="btn btn-sm btn-outline-primary SharpSearch__button">
                    <svg class="align-middle" width="1.25em" height="1.25em" viewBox="0 0 16 16" fill-rule="evenodd">
                        <path d="M6 2c2.2 0 4 1.8 4 4s-1.8 4-4 4-4-1.8-4-4 1.8-4 4-4zm0-2C2.7 0 0 2.7 0 6s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zM16 13.8L13.8 16l-3.6-3.6 2.2-2.2z"></path>
                        <path d="M16 13.8L13.8 16l-3.6-3.6 2.2-2.2z"></path>
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
            disabled: Boolean,
        },
        data() {
            return {
                search: null,
            }
        },
        watch: {
            value: {
                immediate: true,
                handler: 'handleValueChanged',
            }
        },
        computed: {
            clearVisible() {
                return this.search?.length > 0;
            },
        },
        methods: {
            handleValueChanged(value) {
                this.search = value;
            },
            handleClearButtonClicked() {
                this.search = '';
                this.$emit('submit', '');
                this.$emit('clear');
                this.$refs.input.focus();
            },
            handleFocused() {
                this.$emit('focus');
            },
            handleBlur() {
                this.$emit('blur');
            },
            handleFocusout(e) {
                if(!e.relatedTarget || !this.$el.contains(e.relatedTarget)) {
                    this.search = this.value;
                }
            },
            handleSubmitted() {
                this.$emit('submit', this.search);
            },
        },
    }
</script>
