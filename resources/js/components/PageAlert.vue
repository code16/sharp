<script setup lang="ts">
    import { PageAlertData } from "@/types";
    import { Alert, AlertDescription } from "@/components/ui/alert";
    import { Info, TriangleAlert, OctagonAlert } from 'lucide-vue-next';
    import { Button } from "@/components/ui/button";
    import { isSharpLink } from "@/utils/url";
    import { Link } from '@inertiajs/vue3';

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
        <div class="flex items-center gap-4">
            <AlertDescription class="flex-1" :class="{
                'font-medium': pageAlert.level === 'warning' || pageAlert.level === 'danger',
                'text-foreground': pageAlert.level === 'warning',
            }">
                <div v-html="pageAlert.text"></div>
            </AlertDescription>
            <template v-if="pageAlert.buttonLabel && window.location.origin + $page.url !== pageAlert.buttonUrl">
                <Button class="-my-2" :as="isSharpLink(pageAlert.buttonUrl) ? Link : 'a'" size="sm" variant="link" :href="pageAlert.buttonUrl">
                    {{ pageAlert.buttonLabel }}
                </Button>
            </template>
        </div>
    </Alert>
</template>
