<script setup lang="ts">
    import Layout from "@/Layouts/Layout.vue";
    import { Card, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

    defineProps<{
        status: number,
        message: string,
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
                            {{ message }}
                        </CardDescription>
                    </CardHeader>
                </Card>
            </div>
        </div>
    </Layout>
</template>
