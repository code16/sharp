<script setup lang="ts">
    import AuthLayout from "@/Layouts/Auth/AuthLayout.vue";
    import { useForm } from "@inertiajs/vue3";
    import { config } from "@/utils/config";
    import { __ } from "@/utils/i18n";
    import Title from "@/components/Title.vue";
    import { route } from "@/utils/url";
    import { Button } from "@/components/ui/button";
    import { Label } from "@/components/ui/label";
    import { Checkbox } from "@/components/ui/checkbox";
    import { Input } from "@/components/ui/input";
    import { Alert, AlertTitle } from "@/components/ui/alert";
    import AuthCard from "@/Layouts/Auth/AuthCard.vue";
    import { session } from "@/utils/session";
    import { FormItem } from "@/components/ui/form";
    import { Check } from "lucide-vue-next";
    import TemplateRenderer from "@/components/TemplateRenderer.vue";
    import { onMounted } from "vue";
    import {
        startAuthentication,
        browserSupportsWebAuthn,
        browserSupportsWebAuthnAutofill
    } from "@simplewebauthn/browser";
    import { api } from "@/api/api";
    import { Separator } from "@/components/ui/separator";

    const props = defineProps<{
        loginIsEmail: boolean,
        message: string | null,
        prefill?: {
            login: string | null,
            password: string | null,
        },
        passkeyError: string | null,
    }>();


    const form = useForm({
        login: props.prefill?.login,
        password: props.prefill?.password,
        remember: false,
        supports_passkeys: browserSupportsWebAuthn(),
    });

    const passkeyForm = useForm({
        remember: false,
        start_authentication_response: '',
    });

    async function loginWithPasskey(autofill = false) {
        try {
            const response = await api.get(route('passkeys.authentication_options'), {
                ignoreContentType: true,
            });

            const authenticationOptions = response.data;
            const authenticationResponse = await startAuthentication({
                optionsJSON: authenticationOptions,
                useBrowserAutofill: autofill,
            });

            console.log(authenticationResponse);

            passkeyForm.remember = form.remember;
            passkeyForm.start_authentication_response = JSON.stringify(authenticationResponse);

            passkeyForm.post(route('passkeys.login'));
        } catch (error) {
            console.error(error);
        }
    }

    onMounted(async () => {
        if(config('sharp.auth.passkeys.enabled') && await browserSupportsWebAuthnAutofill()) {
            await loginWithPasskey(true);
        }
    });
</script>

<template>
    <AuthLayout>
        <Title>
            {{ __('sharp::pages/auth/login.title') }}
        </Title>

        <template v-if="form.hasErrors || passkeyError">
            <Alert class="mb-4" variant="destructive">
                <AlertTitle class="mb-0">
                    {{ Object.values(form.errors)[0] || passkeyError }}
                </AlertTitle>
            </Alert>
        </template>

        <template v-if="session('status')">
            <Alert class="mb-4" :variant="session('status_level') === 'error' ? 'destructive' : 'default'">
                <template v-if="session('status_level') !== 'error'">
                    <Check class="w-4 h-4"/>
                </template>
                <AlertTitle class="mb-0">
                    {{ session('status') }}
                </AlertTitle>
            </Alert>
        </template>

        <form @submit.prevent="form.post(route('code16.sharp.login.post'))">
            <AuthCard>
                <template #title>
                    {{ __('sharp::pages/auth/login.title') }}
                </template>
                <div class="grid gap-4">
                    <FormItem>
                        <Label for="login">
                            {{
                                loginIsEmail ? __('sharp::pages/auth/login.login_field_for_email') : __('sharp::pages/auth/login.login_field')
                            }}
                        </Label>
                        <Input
                            id="login"
                            :type="loginIsEmail ? 'email' : 'text'"
                            :autocomplete="loginIsEmail ? 'email webauthn' : 'username webauthn'"
                            v-model="form.login"
                            autofocus
                        />
                    </FormItem>
                    <FormItem>
                        <div class="flex items-center">
                            <Label for="password">{{ __('sharp::pages/auth/login.password_field') }}</Label>
                            <template v-if="config('sharp.auth.forgotten_password.enabled') && config('sharp.auth.forgotten_password.show_reset_link_in_login_form')">
                                <a :href="route('code16.sharp.password.request')" class="ml-auto inline-block text-sm underline">
                                    {{ __('sharp::pages/auth/login.forgot_password_link') }}
                                </a>
                            </template>
                        </div>
                        <Input id="password" type="password" v-model="form.password" autocomplete="current-password webauthn" />
                    </FormItem>
                    <template v-if="config('sharp.auth.suggest_remember_me')">
                        <FormItem>
                            <div class="flex items-center space-x-2">
                                <Checkbox id="remember_me" v-model="form.remember" />
                                <Label for="remember_me">
                                    {{ __('sharp::pages/auth/login.remember') }}
                                </Label>
                            </div>
                        </FormItem>
                    </template>
                </div>
                <template #footer>
                    <div class="grid gap-2 w-full">
                        <Button type="submit" class="w-full">
                            {{ __('sharp::pages/auth/login.button') }}
                        </Button>
                        <template v-if="config('sharp.auth.passkeys.enabled') && form.supports_passkeys">
                            <div class="my-3 flex items-center gap-x-6">
                                <Separator class="flex-1" />
                                <div class="text-center text-sm text-muted-foreground [text-box:trim-both_ex_alphabetic]">
                                    {{ __('sharp::pages/auth/login.or_label') }}
                                </div>
                                <Separator class="flex-1" />
                            </div>
                            <Button
                                type="button"
                                variant="outline"
                                class="w-full"
                                @click="loginWithPasskey()"
                            >
                                {{ __('sharp::pages/auth/login.passkey_button') }}
                            </Button>
                        </template>
                    </div>
                </template>
            </AuthCard>
        </form>

        <template v-if="message">
            <div class="mt-4">
                <TemplateRenderer :template="message"/>
            </div>
        </template>
    </AuthLayout>
</template>
