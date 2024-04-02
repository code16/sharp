<script setup lang="ts">
    import AuthLayout from "@/Layouts/Auth/AuthLayout.vue";
    import { useForm } from "@inertiajs/vue3";
    import { __ } from "@/utils/i18n";
    import Title from "@/components/Title.vue";
    import { route } from "@/utils/url";
    import {config} from "@/utils/config";
    import { Button } from "@/components/ui/button";

    defineProps<{
        helpText: string
    }>();

    const form = useForm({
        code: '',
    });
</script>

<template>
    <AuthLayout :show-site-name="config('sharp.auth.login_form.display_app_name')">
        <Title>
            {{ __('sharp::pages/auth/login.title') }}
        </Title>
        <template v-if="form.hasErrors" #prepend>
            <div class="rounded-md bg-red-100 p-4 mb-4">
                <h3 class="text-sm font-medium text-red-800">
                    {{ Object.values(form.errors)[0] }}
                </h3>
            </div>
        </template>

        <div class="mb-3" v-html="helpText">
        </div>

        <form @submit.prevent="form.post(route('code16.sharp.login.2fa.post'))">
            <div class="mb-3">
                <input type="text" name="code" id="code" class="form-control" v-model="form.code" :placeholder="__('sharp::pages/auth/login.code_field')">
            </div>

            <div class="text-center mt-4">
                <Button type="submit" class="w-full">
                    {{ __('sharp::pages/auth/login.button') }}
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
