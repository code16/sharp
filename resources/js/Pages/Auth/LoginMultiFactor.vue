<script setup lang="ts">
    import AuthLayout from "@/Layouts/Auth/AuthLayout.vue";
    import { useForm } from "@inertiajs/vue3";
    import { __ } from "@/utils/i18n";
    import Title from "@/components/Title.vue";
    import { route } from "@/utils/url";
    import { Button } from "@/components/ui/button";
    import { Alert, AlertTitle } from "@/components/ui/alert";
    import AuthCard from "@/Layouts/Auth/AuthCard.vue";
    import { Label } from "@/components/ui/label";
    import { Input } from "@/components/ui/input";
    import { FormItem } from "@/components/ui/form";
    import { MultiFactorMethod } from "@/types";
    import { usePasskeyLogin } from "@/Pages/Auth/usePasskeyLogin";

    const props = defineProps<{
        method: MultiFactorMethod,
        helpText: string,
        passkeyError: string,
        errors: Record<string, string>,
    }>();

    const form = useForm({
        code: '',
    });

    const { loginWithPasskey } = usePasskeyLogin({ autofill: props.method === 'passkey' });
</script>

<template>
    <AuthLayout>
        <Title>
            {{ __('sharp::pages/auth/login.title') }}
        </Title>

        <template v-if="Object.keys(errors).length || passkeyError">
            <Alert class="mb-4" variant="destructive">
                <AlertTitle class="mb-0">
                    {{ Object.values(errors)[0] || passkeyError }}
                </AlertTitle>
            </Alert>
        </template>

        <form @submit.prevent="method === 'passkey' ? loginWithPasskey() : form.post(route('code16.sharp.login.2fa.post'))">
            <AuthCard :empty="method == 'passkey'">
                <template #title>
                    {{ __('sharp::pages/auth/login.title') }}
                </template>
                <template v-if="helpText" #description>
                    <div class="space-y-2" v-html="helpText"></div>
                </template>
                <template v-if="method == 'passkey'">
                    <input class="sr-only" autocomplete="webauthn"></input>
                </template>
                <template v-else>
                    <FormItem>
                        <Label for="code">
                            {{ __('sharp::pages/auth/login.code_field') }}
                        </Label>
                        <Input type="text" id="code" v-model="form.code" />
                    </FormItem>
                </template>
                <template #footer>
                    <Button type="submit" class="w-full">
                        <template v-if="method === 'passkey'">
                            {{ __('sharp::pages/auth/login.passkey_button') }}
                        </template>
                        <template v-else>
                            {{ __('sharp::pages/auth/login.button') }}
                        </template>
                    </Button>
                </template>
            </AuthCard>
        </form>
    </AuthLayout>
</template>
