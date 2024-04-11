<script setup lang="ts">
    import AuthLayout from "@/Layouts/Auth/AuthLayout.vue";
    import { useForm } from "@inertiajs/vue3";
    import { __ } from "@/utils/i18n";
    import Title from "@/components/Title.vue";
    import { route } from "@/utils/url";
    import { Button } from "@/components/ui/button";
    import { Alert, AlertDescription } from "@/components/ui/alert";
    import AuthCard from "@/Layouts/Auth/AuthCard.vue";
    import { Label } from "@/components/ui/label";
    import { Input } from "@/components/ui/input";
    import { FormItem, FormMessage } from "@/components/ui/form";

    defineProps<{
        helpText: string
    }>();

    const form = useForm({
        code: '',
    });
</script>

<template>
    <AuthLayout>
        <Title>
            {{ __('sharp::pages/auth/login.title') }}
        </Title>

        <template v-if="form.hasErrors">
            <Alert class="mb-4" variant="destructive">
                <AlertDescription>
                    {{ Object.values(form.errors)[0] }}
                </AlertDescription>
            </Alert>
        </template>

        <form @submit.prevent="form.post(route('code16.sharp.login.2fa.post'))">
            <AuthCard>
                <template #title>
                    {{ __('sharp::pages/auth/login.title') }}
                </template>
                <template v-if="helpText" #description>
                    <div class="space-y-2" v-html="helpText"></div>
                </template>
                <FormItem>
                    <Label for="code">
                        {{ __('sharp::pages/auth/login.code_field') }}
                    </Label>
                    <Input type="text" id="code" v-model="form.code" />
                </FormItem>
                <template #footer>
                    <Button type="submit" class="w-full">
                        {{ __('sharp::pages/auth/login.button') }}
                    </Button>
                </template>
            </AuthCard>
        </form>
    </AuthLayout>
</template>
