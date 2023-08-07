<script setup lang="ts">
    import { Breadcrumb } from '@sharp/ui';
    import LocaleSelect from "./ui/LocaleSelect.vue";
    import { BreadcrumbData } from "@/types";
    import { config } from "@/utils/config";

    defineProps<{
        breadcrumb: BreadcrumbData,
        locales?: Array<string>,
    }>();
</script>

<template>
    <div class="action-bar mt-4 mb-3">
        <div class="row align-items-center gx-3">
            <div class="col">
                <template v-if="config('sharp.display_breadcrumb')">
                    <Breadcrumb :items="breadcrumb.items" />
                </template>
            </div>
            <template v-if="locales?.length">
                <div class="col-auto">
                    <LocaleSelect
                        outline
                        right
                        :locale="currentLocale"
                        :locales="locales"
                        @change="$emit('locale-change', $event)"
                    />
                </div>
            </template>
        </div>
    </div>
</template>
