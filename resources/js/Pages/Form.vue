<script setup lang="ts">
    import { Form } from "@sharp/form";
    import ActionBarForm from '@sharp/form/src/components/ActionBar.vue';
    import Layout from "../Layouts/Layout.vue";
    import { BreadcrumbData, FormData } from "@/types";
    import { router } from "@inertiajs/vue3";

    defineProps<{
        form: FormData,
        breadcrumb: BreadcrumbData,
        errors: object,
    }>();

    function submit(data) {
        const { entityKey, instanceId } = route().params;
        if(route().current('code16.sharp.form.create')) {
            router.post(route('code16.sharp.form.store', { entityKey }), data);
        } else if(route().current('code16.sharp.form.edit')) {
            router.post(route('code16.sharp.form.update', { entityKey, instanceId }), data);
        }
    }
</script>

<template>
    <Layout>
        <div class="FormPage">
            <div class="container">
                <Form
                    :form="form"
                    :form-errors="errors"
                    :entity-key="route().params.entityKey"
                    :instance-id="route().params.instanceId"
                    @submit="submit"
                >
                    <template v-slot:action-bar="{ props, listeners }">
                        <ActionBarForm
                            v-bind="props"
                            v-on="listeners"
                            :breadcrumb="breadcrumb"
                        />
                    </template>
                </Form>
            </div>
        </div>
    </Layout>
</template>
