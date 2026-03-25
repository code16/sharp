<script setup lang="ts">
    import AuthLayout from "@/Layouts/Auth/AuthLayout.vue";
    import { useForm } from "@inertiajs/vue3";
    import { __ } from "@/utils/i18n";
    import Title from "@/components/Title.vue";
    import { route } from "@/utils/url";
    import { Button } from "@/components/ui/button";
    import { Label } from "@/components/ui/label";
    import { Input } from "@/components/ui/input";
    import AuthCard from "@/Layouts/Auth/AuthCard.vue";
    import { FormItem } from "@/components/ui/form";
    import { startRegistration } from "@simplewebauthn/browser";
    import { api } from "@/api/api";
    import { FieldError } from "@/components/ui/field";

    const props = defineProps<{
        prompt: boolean,
        cancelUrl: string,
    }>();

    const form = useForm({
        name: '',
        passkey: '',
    });

    async function submit() {
        form.clearErrors();

        try {
            const optionsResponse = await api.post(route('code16.sharp.passkeys.validate'), {
                name: form.name,
            });

            const registrationResponse = await startRegistration({
                optionsJSON: optionsResponse.data.passkeyOptions,
            });

            form.passkey = JSON.stringify(registrationResponse);

            form.post(route('code16.sharp.passkeys.store'));
        } catch (error) {
            if (error.response?.status === 422) {
                form.setError({ name: error.response.data.errors?.name?.[0] });
            } else {
                console.error(error);
            }
        }
    }
</script>

<template>
    <AuthLayout>
        <Title>
            {{ __('sharp::pages/auth/passkeys.title') }}
        </Title>

        <form @submit.prevent="submit">
            <AuthCard>
                <template #title>
                    {{ __('sharp::pages/auth/passkeys.title') }}
                </template>

                <div class="grid gap-4">
                    <template v-if="prompt">
                        <p class="text-sm text-muted-foreground">
                            {{ __('sharp::pages/auth/passkeys.prompt_version.description') }}
                        </p>
                    </template>

                    <FormItem>
                        <Label for="name">
                            {{ __('sharp::pages/auth/passkeys.name_field') }}
                        </Label>
                        <Input
                            id="name"
                            type="text"
                            v-model="form.name"
                            autofocus
                        />
                        <template v-if="form.errors.name">
                            <FieldError class="mt-2">
                                {{ form.errors.name }}
                            </FieldError>
                        </template>
                    </FormItem>
                </div>

                <template #footer>
                    <div class="grid gap-2 w-full">
                        <Button type="submit" class="w-full" :disabled="form.processing">
                            {{ prompt ? __('sharp::pages/auth/passkeys.prompt_version.button') : __('sharp::pages/auth/passkeys.account_version.button') }}
                        </Button>

                        <template v-if="prompt">
                            <Button
                                type="button"
                                variant="ghost"
                                class="w-full"
                                as="a"
                                :href="route('code16.sharp.home')"
                            >
                                {{ __('sharp::pages/auth/passkeys.prompt_version.skip_prompt_button') }}
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                class="w-full"
                                @click="useForm({}).post(route('code16.sharp.passkeys.skip-prompt'))"
                            >
                                {{ __('sharp::pages/auth/passkeys.prompt_version.never_ask_again_button') }}
                            </Button>
                        </template>
                        <template v-else>
                            <Button
                                type="button"
                                variant="ghost"
                                class="w-full"
                                as="a"
                                :href="cancelUrl"
                            >
                                {{ __('sharp::pages/auth/passkeys.account_version.cancel_button') }}
                            </Button>
                        </template>
                    </div>
                </template>
            </AuthCard>
        </form>
    </AuthLayout>
</template>
