<script setup lang="ts">
    import { Card } from "@/components/ui/card";

    defineProps<{ noPadding?: boolean }>();
</script>

<template>
    <div class="@container">
        <div :class="[noPadding ? 'px-0' : 'max-lg:@[66rem]:px-4 lg:@[67rem]:px-6']">
            <div class="@container max-w-5xl mx-auto px-0 2xl:max-w-[87rem] @[67rem]:max-w-[87rem]">
                <Card class="rounded-none border-x-0 *:px-4 lg:*:px-6 @5xl:border-x @5xl:rounded-lg">
                    <slot />
                </Card>
            </div>
        </div>
    </div>
</template>
