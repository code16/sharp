<script setup lang="ts">
    import AuthLayout from "@/Layouts/Auth/AuthLayout.vue";
    import Title from "@/components/Title.vue";
    import { __ } from "@/utils/i18n";
    import { useForm } from "@inertiajs/vue3";
    import { Button } from '@/components/ui/button';
    import { route } from "@/utils/url";
    import AuthCard from "@/Layouts/Auth/AuthCard.vue";
    import { FormItem, FormMessage } from "@/components/ui/form";
    import { Label } from "@/components/ui/label";
    import { Input } from "@/components/ui/input";
    import { session } from "@/utils/session";
    import { Alert, AlertTitle } from "@/components/ui/alert";
    import { Check } from "lucide-vue-next";

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
            {{ __('sharp::pages/auth/reset-password.title') }}
        </Title>

        <form @submit.prevent="form.post(route('code16.sharp.password.reset.post'), {
            onFinish: () => { form.reset('password', 'password_confirmation') },
        })">
            <AuthCard>
                <template #title>
                    {{ __('sharp::pages/auth/reset-password.title') }}
                </template>
                <template #description>
                    {{ __('sharp::pages/auth/reset-password.description') }}
                </template>
                <div class="grid gap-4">
                    <FormItem>
                        <Label for="email">
                            {{ __('sharp::pages/auth/reset-password.email_field') }}
                        </Label>
                        <Input id="email" type="email" v-model="form.email" />
                        <FormMessage :message="form.errors.email" />
                    </FormItem>
                    <FormItem>
                        <Label for="password">
                            {{ __('sharp::pages/auth/reset-password.password_field') }}
                        </Label>
                        <Input id="password" type="password" v-model="form.password" />
                        <FormMessage :message="form.errors.password" />
                    </FormItem>
                    <FormItem>
                        <Label for="password_confirmation">
                            {{ __('sharp::pages/auth/reset-password.password_confirmation_field') }}
                        </Label>
                        <Input id="password_confirmation" type="password" v-model="form.password_confirmation" />
                        <FormMessage :message="form.errors.password_confirmation" />
                    </FormItem>
                </div>
                <template #footer>
                    <Button class="w-full" type="submit">
                        {{ __('sharp::pages/auth/reset-password.submit') }}
                    </Button>
                </template>
            </AuthCard>
        </form>
    </AuthLayout>
</template>
