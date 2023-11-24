<script setup lang="ts">
    import AuthLayout from "@/Layouts/AuthLayout.vue";
    import Title from "@/components/Title.vue";
    import { __ } from "@/utils/i18n";
    import { useForm } from "@inertiajs/vue3";
    import TextInput from "@sharp/form/src/components/fields/text/TextInput.vue";
    import { Button } from "@sharp/ui";
    import { route } from "@/utils/url";

    const props = defineProps<{
        token: string,
        email: string,
    }>();

    const form = useForm({
        token: props.token,
        email: props.email,
        password: '',
        password_confirmation: '',
    });
</script>

<template>
    <AuthLayout>
        <Title>
            {{ __('sharp::auth.reset_password.page_title') }}
        </Title>

        <div class="text-sm text-gray-500 leading-6 mb-6">
            {{ __('sharp::pages/auth/reset-password.description') }}
        </div>

        <form @submit.prevent="form.post(route('code16.sharp.password.reset.post'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        })">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium leading-6 text-gray-900 mb-2" for="email">
                        {{ __('sharp::pages/auth/reset-password.email_field') }}
                    </label>
                    <TextInput
                        id="email"
                        v-model="form.email"
                        :has-error="!!form.errors.email"
                    />
                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                        {{ form.errors.email }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium leading-6 text-gray-900 mb-2" for="password">
                        {{ __('sharp::pages/auth/reset-password.password_field') }}
                    </label>
                    <TextInput
                        id="password"
                        v-model="form.password"
                        :has-error="!!form.errors.password"
                        type="password"
                        autofocus
                    />
                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                        {{ form.errors.password }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium leading-6 text-gray-900 mb-2" for="password_confirmation">
                        {{ __('sharp::pages/auth/reset-password.password_confirmation_field') }}
                    </label>
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :has-error="!!form.errors.password_confirmation"
                        type="password"
                    />
                    <p v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-600">
                        {{ form.errors.password_confirmation }}
                    </p>
                </div>
            </div>
            <Button class="w-full mt-6" type="submit">
                {{ __('sharp::pages/auth/reset-password.submit') }}
            </Button>
        </form>
    </AuthLayout>
</template>
