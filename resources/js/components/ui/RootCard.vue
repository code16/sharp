<script setup lang="ts">
    import { Card } from "@/components/ui/card";

    defineProps<{ noPadding?: boolean }>();
</script>

<template>
    <div class="@container">
        <div :class="[noPadding ? 'px-0' : '']">
            <div class="@container/root-card mx-auto px-0 max-w-348">
                <Card class="relative rounded-none border-x-0 *:px-4 @3xl:*:px-6 @[87rem]:border-x @[87rem]:rounded-lg" data-sticky-top-container>
                    <slot />
                </Card>
            </div>
        </div>
    </div>
</template>
