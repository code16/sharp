<script setup lang="ts">

import { config } from "@/utils/config";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import SharpIcon from "@/svg/SharpIcon.vue";

</script>

<template>
    <Card>
        <CardHeader>
            <h4 class="text-sm text-muted-foreground">
                {{ config("sharp.name") }}
            </h4>
            <CardTitle class="text-2xl">
                <slot name="title"></slot>
            </CardTitle>
<!--            <div class="flex items-center min-h-[2rem]">-->
<!--                <template v-if="config('sharp.auth.login_form.display_app_name')">-->
<!--                    <CardTitle class="text-2xl">-->
<!--                        {{ config("sharp.name") }}-->
<!--                    </CardTitle>-->
<!--                </template>-->
<!--                <div :class="config('sharp.auth.login_form.display_app_name') ? 'opacity-25 ml-auto' : ''">-->
<!--                    <template v-if="config('sharp.auth.login_form.logo_url')">-->
<!--                        <img :src="config('sharp.auth.login_form.logo_url')" :alt="config('sharp.name')" width="100">-->
<!--                    </template>-->
<!--                    <template v-else>-->
<!--                        <SharpIcon class="w-[100px]" />-->
<!--                    </template>-->
<!--                </div>-->
<!--            </div>-->
            <template v-if="$slots.description">
                <CardDescription>
                    <slot name="description"/>
                </CardDescription>
            </template>
        </CardHeader>
        <CardContent>
            <slot />
        </CardContent>
        <CardFooter>
            <slot name="footer" />
        </CardFooter>
    </Card>
</template>

