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
        <div class="container">
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
    </Layout>
</template>
