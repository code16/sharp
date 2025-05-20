<script setup lang="ts">
    import { usePage } from "@inertiajs/vue3";
    import { NotificationData } from "@/types";
    import { toast } from "vue-sonner";
    import Sonner from "@/components/ui/sonner/Sonner.vue";
    import { nextTick, onMounted, watch } from "vue";

    async function showNotifications() {
        const notifications = usePage().props.notifications as NotificationData[];
        for (const notification of notifications ?? []) {
            const options = {
                description: notification.message,
                duration: notification.autoHide ? 4000 : Infinity,
                closeButton: !notification.autoHide,
            };
            if (notification.level === 'success') {
                toast.success(notification.title, options);
            } else if (notification.level === 'danger') {
                toast.error(notification.title, options);
            } else if (notification.level === 'warning') {
                toast.warning(notification.title, options);
            } else {
                toast.info(notification.title, options);
            }
            await nextTick();
        }
    }

    watch(() => usePage().props.notifications, () => {
        showNotifications();
    });

    onMounted(() => {
        showNotifications();
    });
</script>

<template>
    <Sonner />
</template>
