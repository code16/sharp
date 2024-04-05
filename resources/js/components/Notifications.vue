<script setup lang="ts">
    import { usePage } from "@inertiajs/vue3";
    import { NotificationData } from "@/types";
    import { toast } from "vue-sonner";
    import Sonner from "@/components/ui/sonner/Sonner.vue";
    import { onMounted } from "vue";

    const notifications = usePage().props.notifications as NotificationData[]

    onMounted(() => {
        notifications?.forEach(notification => {
            const options = {
                description: notification.message,
                duration: notification.autoHide ? 3000 : Infinity,
                closeButton: !notification.autoHide,
            };
            if(notification.level === 'success') {
                toast.success(notification.title, options);
            } else if(notification.level === 'danger') {
                toast.error(notification.title, options);
            } else if(notification.level === 'warning') {
                toast.warning(notification.title, options);
            } else {
                toast(notification, options); // no icon for info level
            }
        });
    });
</script>

<template>
    <Sonner close-button />
</template>
