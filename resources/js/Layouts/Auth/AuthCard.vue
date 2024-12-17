<script setup lang="ts">
    import { config } from "@/utils/config";
    import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
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

