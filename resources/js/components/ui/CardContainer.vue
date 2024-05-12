<script setup lang="ts">
    defineProps<{ noPadding?: boolean }>();
</script>

<template>
    <div class="@container">
        <div :class="[noPadding ? 'px-0' : 'max-lg:@[66rem]:px-4 lg:@[67rem]:px-6']">
            <div class="@container max-w-5xl mx-auto px-0 2xl:max-w-[87rem] @[67rem]:max-w-[87rem]">
                <slot />
            </div>
        </div>
    </div>
</template>
