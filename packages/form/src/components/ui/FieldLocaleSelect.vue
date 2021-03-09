<template>
    <div class="SharpFieldLocaleSelect">
        <div class="row gx-1">
            <template v-for="locale in locales">
                <div class="col-auto">
                    <button
                        class="SharpFieldLocaleSelect__btn ml-2"
                        :class="{
                            'SharpFieldLocaleSelect__btn--active': isActive(locale),
                            'SharpFieldLocaleSelect__btn--empty': isEmpty(locale),
                            'SharpFieldLocaleSelect__btn--error': hasError(locale),
                        }"
                        @click="handleButtonClicked(locale)"
                    >
                        {{ locale }}
                    </button>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            locales: {
                type: Array,
                required: true,
            },
            currentLocale: {
                type: String,
                required: true,
            },
            fieldValue: [String, Number, Boolean, Object, Array],
            isLocaleObject: Boolean,
            errors: Array,
        },
        methods: {
            isActive(locale) {
                return this.currentLocale === locale;
            },
            isEmpty(locale) {
                const value = this.isLocaleObject ? (this.fieldValue || {})[locale] : this.fieldValue;
                return Array.isArray(value) ? !value.length : !value;
            },
            hasError(locale) {
                return this.errors?.includes(locale);
            },
            handleButtonClicked(locale) {
                this.$emit('change', locale);
            },
        }
    }
</script>
