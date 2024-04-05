<script setup lang="ts">
    import { usePage } from "@inertiajs/vue3";
    import { NotificationData } from "@/types";
    import { toast } from "vue-sonner";
    import Sonner from "@/components/ui/sonner/Sonner.vue";
    import { onMounted } from "vue";

    const notifications = usePage().props.notifications as NotificationData[]

    onMounted(() => {
        notifications?.forEach(notification => {
            toast(notification.title, {
                description: notification.message,
                duration: notification.autoHide ? 3000 : Infinity,
                closeButton: !notification.autoHide,
            });
        });
    });
</script>

<template>
    <Sonner close-button />
</template>
