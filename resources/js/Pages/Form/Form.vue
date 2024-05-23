<script setup lang="ts">
    import { Form } from "@/form/Form";
    import Layout from "@/Layouts/Layout.vue";
    import { BreadcrumbData, FormData } from "@/types";
    import { router } from "@inertiajs/vue3";
    import { route } from "@/utils/url";
    import Title from "@/components/Title.vue";
    import { config } from "@/utils/config";
    import PageBreadcrumb from "@/components/PageBreadcrumb.vue";
    import { ref, watchEffect } from "vue";
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { vSticky } from "@/directives/sticky";
    import RootCard from "@/components/ui/RootCard.vue";
    import { CardContent, CardHeader, CardTitle } from "@/components/ui/card";

    const props = defineProps<{
        form: FormData,
        breadcrumb: BreadcrumbData,
        errors: { [key:string]: string },
    }>();

    const { entityKey, instanceId } = route().params as { entityKey: string, instanceId?: string };
    const form = new Form(props.form, entityKey, instanceId);
    const loading = ref(false);
    const bottomBarStuck = ref(false);

    watchEffect(() => {
        form.errors = props.errors;
    });

    function submit() {
        const { parentUri, entityKey, instanceId } = route().params;
        const onStart = () => { loading.value = true };
        const onFinish = () => { loading.value = false };

        if(route().current('code16.sharp.form.create')) {
            router.post(
                route('code16.sharp.form.store', { parentUri, entityKey }),
                form.serializedData,
                { onStart, onFinish }
            );
        } else if(route().current('code16.sharp.form.edit')) {
            router.post(
                route('code16.sharp.form.update', { parentUri, entityKey, instanceId }),
                form.serializedData,
                { onStart, onFinish }
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
            :entity-key="entityKey"
            :instance-id="instanceId"
            :show-error-alert="Object.values(props.errors).length > 0"
            @submit="submit"
        >
            <template #title>
                {{ breadcrumb.items.at(-1).documentTitleLabel }}
            </template>
            <template #footer>
                <div class="flex gap-4">
                    <Button as="a" :href="breadcrumb.items.at(-2)?.url" variant="outline">
                        <template v-if="form.canEdit">
                            {{ __('sharp::action_bar.form.cancel_button') }}
                        </template>
                        <template v-else>
                            {{ __('sharp::action_bar.form.back_button') }}
                        </template>
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
