<script setup lang="ts">
    import { Form } from "@sharp/form";
    import Layout from "@/Layouts/Layout.vue";
    import { BreadcrumbData, FormData } from "@/types";
    import { router } from "@inertiajs/vue3";
    import { route } from "@/utils/url";
    import Title from "@/components/Title.vue";
    import { config } from "@/utils/config";
    import Breadcrumb from "@/components/Breadcrumb.vue";

    defineProps<{
        form: FormData,
        breadcrumb: BreadcrumbData,
        errors: object,
    }>();

    function submit(data) {
        const { uri, entityKey, instanceId } = route().params;
        if(route().current('code16.sharp.form.create')) {
            router.post(route('code16.sharp.form.store', { uri, entityKey }), data);
        } else if(route().current('code16.sharp.form.edit')) {
            router.post(route('code16.sharp.form.update', { uri, entityKey, instanceId }), data);
        }
    }
</script>

<template>
    <Layout>
        <Title :breadcrumb="breadcrumb" />

        <div class="container">
            <Form
                :form="form"
                :form-errors="errors"
                :entity-key="route().params.entityKey"
                :instance-id="route().params.instanceId"
                is-page
                @submit="submit"
            >
                <template v-slot:title>
                    <template v-if="config('sharp.display_breadcrumb')">
                        <Breadcrumb :breadcrumb="breadcrumb" />
                    </template>
                </template>
            </Form>
        </div>
    </Layout>
</template>
