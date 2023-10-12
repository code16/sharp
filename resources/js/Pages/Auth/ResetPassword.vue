<script setup lang="ts">
    import AuthLayout from "@/Layouts/AuthLayout.vue";
    import Title from "@/components/Title.vue";
    import { __ } from "sharp/utils/i18n";
    import { useForm } from "@inertiajs/vue3";
    import TextInput from "@sharp/form/src/components/fields/text/TextInput.vue";

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

        <template #title>
            {{ __('sharp::auth.reset_password.page_title') }}
        </template>

        <form @submit.prevent="form.post(route('code16.sharp.password.reset.post'))">
            <TextInput
                id="email"

                v-model="form.email"
                :has-error="!!form.errors.email"
                autofocus
            />
        </form>
    </AuthLayout>
</template>
