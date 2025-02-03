<script setup lang="ts">
    import AuthLayout from "@/Layouts/Auth/AuthLayout.vue";
    import Title from "@/components/Title.vue";
    import { __ } from "@/utils/i18n";
    import { useForm } from "@inertiajs/vue3";
    import { Button } from '@/components/ui/button';
    import { route } from "@/utils/url";
    import { session } from "@/utils/session";
    import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
    import { Check } from "lucide-vue-next";
    import AuthCard from "@/Layouts/Auth/AuthCard.vue";
    import { FormItem, FormMessage } from "@/components/ui/form";
    import { Input } from "@/components/ui/input";
    import { Label } from "@/components/ui/label";

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

        <template v-if="session('status')">
            <Alert class="mb-4">
                <Check class="w-4 h-4" />
                <AlertTitle>
                    {{ session('status_title') }}
                </AlertTitle>
                <AlertDescription>
                    {{ session('status') }}
                </AlertDescription>
            </Alert>
        </template>

        <form @submit.prevent="form.post(route('code16.sharp.password.request.post'))">
            <AuthCard>
                <template #title>
                    {{ __('sharp::pages/auth/forgot-password.title') }}
                </template>
                <template #description>
                    {{ __('sharp::pages/auth/forgot-password.description') }}
                </template>
                <FormItem>
                    <Label for="email">
                        {{ __('sharp::pages/auth/forgot-password.email_field')}}
                    </Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        autofocus
                    />
                    <FormMessage :message="form.errors.email" />
                </FormItem>
                <template #footer>
                    <Button class="w-full" type="submit">
                        {{ __('sharp::pages/auth/forgot-password.submit') }}
                    </Button>
                </template>
            </AuthCard>
        </form>
    </AuthLayout>
</template>
