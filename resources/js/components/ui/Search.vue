<template>
    <div class="SharpSearch"
        :class="{ 'SharpSearch--focused': focused || search }"
        role="search"
    >
        <form class="h-100" @submit.prevent="handleSubmitted">
            <label id="ab-search-label" class="SharpSearch__label visually-hidden" for="ab-search-input">{{ placeholder }}</label>
            <div class="input-group input-group-sm flex-nowrap">
                <div class="position-relative flex-fill" style="min-width: 0">
                    <input class="SharpSearch__input form-control form-control-sm h-100 fs-8"
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

                <button class="btn btn-sm btn-outline-primary d-inline-flex SharpSearch__button">
                    <!-- heroicons: solid/20/search -->
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
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
                focused: false,
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
                this.focused = true;
            },
            handleBlur() {
                this.$emit('blur');
                this.focused = false;
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
