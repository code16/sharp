<script setup lang="ts">
    import AuthLayout from "@/Layouts/AuthLayout.vue";
    import Title from "@/components/Title.vue";
    import { __ } from "@/utils/i18n";
    import { useForm } from "@inertiajs/vue3";
    import TextInput from "@/form/components/fields/text/TextInput.vue";
    import { Button } from '@/components/ui/button';
    import { route } from "@/utils/url";

    const props = defineProps<{
        status: string,
    }>();

    const form = useForm({
        email: '',
    });
</script>

<template>
    <AuthLayout>
        <Title>
            {{ __('sharp::pages/auth/forgot-password.title') }}
        </Title>

        <div class="text-sm text-gray-500 leading-6 mb-4">
            {{ __('sharp::pages/auth/forgot-password.description') }}
        </div>

        <form @submit.prevent="form.post(route('code16.sharp.password.request.post'))">
            <TextInput
                id="email"
                v-model="form.email"
                :has-error="!!form.errors.email"
                :placeholder="__('sharp::pages/auth/forgot-password.email_field')"
                autofocus
            />
            <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                {{ form.errors.email }}
            </p>

            <div class="mt-4">
                <Button class="w-full" type="submit">
                    {{ __('sharp::pages/auth/forgot-password.submit') }}
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
