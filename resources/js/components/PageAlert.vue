<script setup lang="ts">
    import { PageAlertData } from "@/types";
    import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
    import { Info, TriangleAlert, CircleAlert } from 'lucide-vue-next';

    defineProps<{
        pageAlert: PageAlertData,
    }>();
</script>

<template>
    <Alert :variant="
        pageAlert.level === 'danger' ? 'destructive'
        : pageAlert.level === 'primary' ? 'primary'
        : 'default'
    ">
        <template v-if="pageAlert.level === 'danger'">
            <CircleAlert class="w-4 h-4"/>
        </template>
        <template v-else-if="pageAlert.level === 'warning'">
            <TriangleAlert class="w-4 h-4 text-destructive" />
        </template>
        <template v-else>
            <Info class="w-4 h-4" />
        </template>
        <AlertDescription :class="{ 'font-medium': pageAlert.level === 'warning' || pageAlert.level === 'danger' }">
            <div v-html="pageAlert.text"></div>
        </AlertDescription>
    </Alert>
</template>
