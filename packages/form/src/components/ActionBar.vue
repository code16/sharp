<template>
    <div class="action-bar my-3">
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

            <template v-if="showDeleteButton">
                <div class="col-auto">
                    <Dropdown outline right>
                        <template v-slot:text>
                            {{ l('entity_list.commands.instance.label') }}
                        </template>
                        <DropdownItem :disabled="loading" @click="handleDeleteClicked">
                            {{ l('action_bar.form.delete_button') }}
                        </DropdownItem>
                    </Dropdown>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { Breadcrumb, Dropdown, DropdownItem } from 'sharp-ui';
    import { Localization } from "sharp/mixins";
    import LocaleSelect from "./ui/LocaleSelect.vue";

    export default {
        name: 'SharpActionBarForm',
        mixins: [Localization],
        components: {
            LocaleSelect,
            Breadcrumb,
            Dropdown,
            DropdownItem,
        },
        props: {
            showSubmitButton: Boolean,
            showDeleteButton: Boolean,
            showBackButton: Boolean,
            create: Boolean,
            uploading: Boolean,
            loading: Boolean,
            breadcrumb: Array,
            showBreadcrumb: Boolean,
            hasDeleteConfirmation: Boolean,
            currentLocale: String,
            locales: Array,
        },
        methods: {
            handleDeleteClicked() {
                this.$emit('delete');
            },
            handleLocaleChanged(locale) {
                this.$emit('locale-change', locale);
            },
        },
    }
</script>
