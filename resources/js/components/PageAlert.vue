<script setup lang="ts">
    import { PageAlertData } from "@/types";
    import { Alert, AlertDescription } from "@/components/ui/alert";
    import { Info, TriangleAlert, OctagonAlert } from 'lucide-vue-next';

    defineProps<{
        pageAlert: PageAlertData,
    }>();
</script>

<template>
    <Alert :variant="
        pageAlert.level === 'danger' ? 'destructive'
        : pageAlert.level === 'warning' ? 'destructive'
        : pageAlert.level === 'primary' ? 'primary'
        : pageAlert.level === 'info' ? 'primary'
        : 'default'
    ">
        <template v-if="pageAlert.level === 'danger'">
            <OctagonAlert class="w-4 h-4"/>
        </template>
        <template v-else-if="pageAlert.level === 'warning'">
            <TriangleAlert class="w-4 h-4 text-foreground" />
        </template>
        <template v-else>
            <Info class="w-4 h-4" />
        </template>
        <AlertDescription :class="{
            'font-medium': pageAlert.level === 'warning' || pageAlert.level === 'danger',
            'text-foreground': pageAlert.level === 'warning',
        }">
            <div v-html="pageAlert.text"></div>
        </AlertDescription>
    </Alert>
</template>
