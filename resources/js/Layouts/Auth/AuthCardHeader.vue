<script setup lang="ts">
    import { config } from "@/utils/config";
    import { CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
    import SharpIcon from "@/svg/SharpIcon.vue";

    defineProps<{
        description?: string|null,
    }>();
</script>

<template>
    <CardHeader>
        <div class="flex items-center min-h-[2rem]">
            <template v-if="config('sharp.auth.login_form.display_app_name')">
                <CardTitle class="text-2xl">
                    {{ config("sharp.name") }}
                </CardTitle>
            </template>
            <div :class="config('sharp.auth.login_form.display_app_name') ? 'opacity-25 ml-auto' : ''">
                <template v-if="config('sharp.auth.login_form.logo_url')">
                    <img :src="config('sharp.auth.login_form.logo_url')" :alt="config('sharp.name')" width="100">
                </template>
                <template v-else>
                    <SharpIcon class="w-[100px]" />
                </template>
            </div>
        </div>
        <template v-if="description">
            <CardDescription>
                {{ description }}
            </CardDescription>
        </template>
    </CardHeader>
</template>
