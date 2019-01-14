<template>
    <div class="SharpFieldLocaleSelector">
        <template v-for="locale in locales">
            <button
                class="SharpFieldLocaleSelector__btn ml-2"
                :class="{
                    'SharpFieldLocaleSelector__btn--active': isActive(locale),
                    'SharpFieldLocaleSelector__btn--empty': isEmpty(locale),
                }"
                @click="handleButtonClicked(locale)"
            >
                {{ locale }}
            </button>
        </template>
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
        },
        methods: {
            isActive(locale) {
                return this.currentLocale === locale;
            },
            isEmpty(locale) {
                const value = this.isLocaleObject ? (this.fieldValue || {})[locale] : this.fieldValue;
                return Array.isArray(value) ? !value.length : !value;
            },
            handleButtonClicked(locale) {
                this.$emit('change', locale);
            },
        }
    }
</script>