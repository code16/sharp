<script setup lang="ts">
    import { Form } from "@/form/Form";
    import Layout from "@/Layouts/Layout.vue";
    import { BreadcrumbData, FormData } from "@/types";
    import { router, Link } from "@inertiajs/vue3";
    import { route } from "@/utils/url";
    import Title from "@/components/Title.vue";
    import { config } from "@/utils/config";
    import PageBreadcrumb from "@/components/PageBreadcrumb.vue";
    import { ref, watch } from "vue";
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';

    const props = defineProps<{
        form: FormData,
        breadcrumb: BreadcrumbData,
        errors: { [key:string]: string },
    }>();

    const { entityKey, instanceId } = route().params as { entityKey: string, instanceId?: string };
    const form = new Form(props.form, entityKey, instanceId);
    const loading = ref(false);
    const showErrorAlert = ref(false);
    const errorAlertMessage = ref('');

    watch(() => form.errors, () => {
        if(Object.keys(form.errors).length === 0) {
            showErrorAlert.value = false;
        }
    }, { deep: true });

    function submit() {
        const { parentUri, entityKey, instanceId } = route().params;
        const onStart = () => { loading.value = true };
        const onFinish = () => { loading.value = false };
        const onError = () => {
            form.errors = props.errors;
            showErrorAlert.value = true;
            errorAlertMessage.value = props.errors.error as string | null;
        }

        if(route().current('code16.sharp.form.create')) {
            router.post(
                route('code16.sharp.form.store', { parentUri, entityKey }),
                form.serializedData,
                { onStart, onFinish, onError }
            );
        } else if(route().current('code16.sharp.form.edit')) {
            router.post(
                route('code16.sharp.form.update', { parentUri, entityKey, instanceId }),
                form.serializedData,
                { onStart, onFinish, onError }
            );
        }
    }
</script>

<template>
    <Layout>
        <Title :breadcrumb="breadcrumb" />

        <template #breadcrumb>
            <template v-if="config('sharp.display_breadcrumb')">
                <PageBreadcrumb :breadcrumb="breadcrumb" />
            </template>
        </template>

        <SharpForm
            :form="form"
            :show-error-alert="showErrorAlert"
            :error-alert-message="errorAlertMessage"
            @submit="submit"
        >
            <template #title>
                {{ form.title }}
            </template>
            <template #footer>
                <div class="flex gap-4">
                    <Button variant="outline" as-child>
                        <Link :href="breadcrumb.items.at(-2)?.url">
                            <template v-if="form.canEdit">
                                {{ __('sharp::action_bar.form.cancel_button') }}
                            </template>
                            <template v-else>
                                {{ __('sharp::action_bar.form.back_button') }}
                            </template>
                        </Link>
                    </Button>
                    <template v-if="form.canEdit">
                        <Button style="min-width: 6.5em" :disabled="form.isUploading || loading" @click="submit">
                            <template v-if="form.isUploading">
                                {{ __('sharp::action_bar.form.submit_button.pending.upload') }}
                            </template>
                            <template v-else-if="instanceId || form.config.isSingle">
                                {{ __('sharp::action_bar.form.submit_button.update') }}
                            </template>
                            <template v-else>
                                {{ __('sharp::action_bar.form.submit_button.create') }}
                            </template>
                        </Button>
                    </template>
                </div>
            </template>
        </SharpForm>
    </Layout>
</template>
