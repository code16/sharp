<script setup lang="ts">
    import Layout from "@/Layouts/Layout.vue";
    import { Card, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

    defineProps<{
        status: number,
        message: string,
        previous?: { message: string }
    }>()
</script>

<template>
    <Layout>
        <div class="@container">
            <div class="container pt-6 @3xl:pt-10">
                <Card>
                    <CardHeader>
                        <CardTitle>
                            Error {{ status }}
                        </CardTitle>
                        <CardDescription>
                            <template v-if="previous">
                                <details class="marker:text-xs">
                                    <summary>
                                        <span class="ml-1">
                                            {{ message }}
                                        </span>
                                    </summary>
                                    <div class="mt-2">
                                        {{ previous.message }}
                                    </div>
                                </details>
                            </template>
                            <template v-else>
                                {{ message }}
                            </template>
                        </CardDescription>
                    </CardHeader>
                </Card>
            </div>
        </div>
    </Layout>
</template>
