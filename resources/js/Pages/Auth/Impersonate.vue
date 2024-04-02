<script setup lang="ts">
    import { useForm } from "@inertiajs/vue3";
    import AuthLayout from "@/Layouts/Auth/AuthLayout.vue";
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import Title from "@/components/Title.vue";
    import { config } from "@/utils/config";
    import { route } from "@/utils/url";

    const props = defineProps<{
        impersonateUsers: { [userId:number]: string },
    }>()

    const form = useForm({
        user_id: Object.keys(props.impersonateUsers ?? {})[0],
    });
</script>

<template>
    <Title>
        {{ __('sharp::pages/auth/impersonate.title') }}
    </Title>
    <AuthLayout :show-site-name="config('sharp.auth.login_form.display_app_name')">
        <form @submit.prevent="form.post(route('code16.sharp.impersonate.post'))">
            <div class="mb-3">
                <label class="block text-sm font-medium leading-6 text-gray-900 mb-2" for="user_id">
                    {{ __('sharp::pages/auth/impersonate.user_id_field') }}
                </label>
                <select class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-600 sm:text-sm sm:leading-6" v-model="form.user_id">
                    <template v-for="(email, userId) in impersonateUsers">
                        <option :value="userId">{{ email }}</option>
                    </template>
                </select>
            </div>

            <div class="mt-6">
                <Button class="w-full" type="submit">
                    {{ __('sharp::pages/auth/impersonate.button') }}
                </Button>
            </div>
            <template v-if="config('sharp.auth.forgotten_password.enabled')">
                <div class="mt-4 -mb-4 text-center">
                    <a class="font-medium text-xs text-primary-600 hover:text-primary-500" :href="route('code16.sharp.login')">
                        {{ __('sharp::pages/auth/impersonate.switch_to_password_login') }}
                    </a>
                </div>
            </template>
        </form>
    </AuthLayout>
</template>
