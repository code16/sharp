<script setup lang="ts">
    import { PageAlertData } from "@/types";
    import { ExclamationTriangleIcon, InformationCircleIcon, XCircleIcon } from '@heroicons/vue/20/solid';

    defineProps<{
        pageAlert: PageAlertData,
    }>();
</script>

<template>
    <div class="border-l-4 p-4"
        :class="{
            'border-red-400 bg-red-50 text-red-700': pageAlert.level === 'error',
            'border-blue-400 bg-blue-50 text-blue-700': pageAlert.level === 'info',
            'border-primary-400 bg-primary-50 text-primary-700': pageAlert.level === 'primary',
            'border-gray-400 bg-gray-50 text-gray-700': pageAlert.level === 'secondary',
            'border-yellow-400 bg-yellow-50 text-yellow-700': pageAlert.level === 'warning',
        }"
    >
        <div class="flex">
            <template v-if="['error', 'info', 'warning'].includes(pageAlert.level)">
                <div class="flex-shrink-0 mr-3">
                    <template v-if="pageAlert.level === 'error'">
                        <XCircleIcon class="h-5 w-5 text-red-400" aria-hidden="true" />
                    </template>
                    <template v-if="pageAlert.level === 'info'">
                        <InformationCircleIcon class="h-5 w-5 text-blue-400" aria-hidden="true" />
                    </template>
                    <template v-if="pageAlert.level === 'warning'">
                        <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400" aria-hidden="true" />
                    </template>
                </div>
            </template>
            <div class="text-sm" v-html="pageAlert.text"></div>
        </div>
    </div>
</template>
