
<template>
    <div class="action-bar mt-4 mb-3">
        <div class="row align-items-center gx-3">
            <div class="col">
                <template v-if="showBreadcrumb">
                    <Breadcrumb :items="breadcrumb" />
                </template>
            </div>
            <template v-if="locales && locales.length">
                <div class="col-auto">
                    <LocaleSelect
                        outline
                        right
                        :locale="currentLocale"
                        :locales="locales"
                        @change="handleLocaleChanged"
                    />
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { Breadcrumb, Dropdown, DropdownItem } from '@sharp/ui';
    import LocaleSelect from "./ui/LocaleSelect.vue";

    export default {
        name: 'SharpActionBarForm',
        components: {
            LocaleSelect,
            Breadcrumb,
            Dropdown,
            DropdownItem,
        },
        props: {
            showSubmitButton: Boolean,
            showBackButton: Boolean,
            create: Boolean,
            uploading: Boolean,
            loading: Boolean,
            breadcrumb: Array,
            showBreadcrumb: Boolean,
            currentLocale: String,
            locales: Array,
        },
        methods: {
            handleLocaleChanged(locale) {
                this.$emit('locale-change', locale);
            },
        },
    }
</script>
