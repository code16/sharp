<script setup lang="ts">
    import { Form as FormComponent } from "@sharp/form";
    import { Form } from "@sharp/form/src/Form";
    import Layout from "@/Layouts/Layout.vue";
    import { BreadcrumbData, FormData } from "@/types";
    import { router } from "@inertiajs/vue3";
    import { route } from "@/utils/url";
    import Title from "@/components/Title.vue";
    import { config } from "@/utils/config";
    import Breadcrumb from "@/components/Breadcrumb.vue";
    import { ref, watchEffect } from "vue";
    import { __ } from "@/utils/i18n";
    import { Button } from "@sharp/ui";
    import { vSticky } from "@/directives/sticky";

    const props = defineProps<{
        form: FormData,
        breadcrumb: BreadcrumbData,
        errors: { [key:string]: string },
    }>();

    const { entityKey, instanceId } = route().params;
    const form = new Form(props.form, entityKey, instanceId);
    const loading = ref(false);
    const bottomBarStuck = ref(false);

    watchEffect(() => {
        form.errors = props.errors;
    });

    function submit() {
        const { uri, entityKey, instanceId } = route().params;
        const onStart = () => loading.value = true;
        const onFinish = () => loading.value = false;

        if(route().current('code16.sharp.form.create')) {
            router.post(
                route('code16.sharp.form.store', { uri, entityKey }),
                form.serialize(form.data),
                { onStart, onFinish }
            );
        } else if(route().current('code16.sharp.form.edit')) {
            router.post(
                route('code16.sharp.form.update', { uri, entityKey, instanceId }),
                form.serialize(form.data),
                { onStart, onFinish }
            );
        }
    }
</script>

<template>
    <Layout>
        <Title :breadcrumb="breadcrumb" />

        <div class="container mx-auto">
            <FormComponent
                :form="form"
                :entity-key="entityKey"
                :instance-id="instanceId"
                @submit="submit"
            >
                <template #title>
                    <template v-if="config('sharp.display_breadcrumb')">
                        <Breadcrumb :breadcrumb="breadcrumb" />
                    </template>
                </template>

                <template #prepend>
                    <template v-if="Object.values(errors).length > 0">
                        <div class="alert alert-danger SharpForm__alert" role="alert">
                            <div class="fw-bold">{{ __('sharp::form.validation_error.title') }}</div>
                            <div>{{ __('sharp::form.validation_error.description') }}</div>
                        </div>
                    </template>
                </template>

                <template #append>
                    <div class="sticky bottom-0 px-4 py-3 bg-white border-t"
                        :class="{ 'shadow': bottomBarStuck }"
                        v-sticky
                        @stuck-change="bottomBarStuck = $event.detail"
                        style="z-index: 100; transition: box-shadow .25s ease-in-out"
                    >
                        <div class="flex gap-4">
                            <div class="flex-1">
                            </div>
                            <Button :href="breadcrumb.items.at(-2)?.url" outline>
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
                    </div>
                </template>
            </FormComponent>
        </div>
    </Layout>
</template>
