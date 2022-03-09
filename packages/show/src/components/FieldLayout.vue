<template>
    <div>
        <template v-if="label || localized">
            <div class="row">
                <template v-if="label">
                    <div class="col-auto">
                        <div class="show-field__label form-label">{{ label }}</div>
                    </div>
                </template>
                <template v-if="localized">
                    <div class="col-auto">
                        <FieldLocaleSelect
                            :current-locale="locale"
                            :field-value="value"
                            :locales="locales"
                            @change="handleLocaleChanged"
                        />
                    </div>
                </template>
            </div>
        </template>

        <div class="show-field__content">
            <slot />
        </div>
    </div>
</template>

<script>
    import { FieldLocaleSelect } from "sharp-form";

    export default {
        components: {
            FieldLocaleSelect,
        },
        props: {
            label: String,
            localized: Boolean,
            locale: String,
            locales: Array,
            value: [String, Object],
        },
        methods: {
            handleLocaleChanged(locale) {
                this.$emit('locale-change', locale);
            },
        },
    }
</script>
