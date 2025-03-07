<script setup lang="ts">
    import { Form } from "@/form/Form";
    import Layout from "@/Layouts/Layout.vue";
    import { BreadcrumbData, FormData } from "@/types";
    import { router, Link, usePage } from "@inertiajs/vue3";
    import { route } from "@/utils/url";
    import Title from "@/components/Title.vue";
    import { config } from "@/utils/config";
    import PageBreadcrumb from "@/components/PageBreadcrumb.vue";
    import { onUnmounted, ref, useTemplateRef, watch } from "vue";
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { useResizeObserver } from "@vueuse/core";
    import { slugify } from "@/utils";

    const props = defineProps<{
        form: FormData,
        breadcrumb: BreadcrumbData,
        errors: { [key:string]: string },
        cancelUrl: string,
        endpointUrl: string,
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
        router.post(
            props.endpointUrl,
            form.serializedData,
            {
                onStart: () => { loading.value = true },
                onFinish: () => { loading.value = false },
                onError: () => {
                    form.onError(props.errors);
                    showErrorAlert.value = true;
                    errorAlertMessage.value = props.errors.error as string | null;
                }
            },
        );
    }

    const selectedTabSlug = ref(
        props.form.layout.tabs
            .map(tab => slugify(tab.title))
            .find(tabSlug => new URLSearchParams(location.search).get('tab') == tabSlug)
        ?? slugify(props.form.layout.tabs?.[0]?.title ?? '')
    );

    const el = useTemplateRef<HTMLElement>('el');
    watch(selectedTabSlug, () => {
        if(el.value && el.value.getBoundingClientRect().top < 56) {
            el.value.scrollIntoView();
        }
        const url = new URL(location.href);
        if(props.form.layout.tabs.length > 1) {
            url.searchParams.set('tab', selectedTabSlug.value);
            router.replace({
                url: url.href,
                preserveState: true,
                preserveScroll: true,
            });
        }
    }, { immediate: true });
</script>

<template>
    <Layout>
        <Title :breadcrumb="breadcrumb" />

        <template #breadcrumb>
            <template v-if="config('sharp.display_breadcrumb')">
                <PageBreadcrumb :breadcrumb="breadcrumb" />
            </template>
        </template>

        <div class="@container">
            <div :class="form.pageAlert ? 'mt-4' : 'mt-6 @3xl:mt-10'" ref="el">
                <SharpForm
                    :form="form"
                    :show-error-alert="showErrorAlert"
                    :error-alert-message="errorAlertMessage"
                    v-model:tab="selectedTabSlug"
                    @submit="submit"
                >
                    <template #title>
                        {{ form.title }}
                    </template>
                    <template #footer>
                        <div class="flex gap-4">
                            <Button variant="outline" as-child>
                                <Link :href="props.cancelUrl">
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
            </div>
        </div>
    </Layout>
</template>
