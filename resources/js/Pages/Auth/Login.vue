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

    const props = defineProps<{
        login: string|null,
        password: string|null,
        description: string|null,
    }>();

    const append = document.querySelector('#login-append')?.innerHTML;
    const form = useForm({
        login: props.login,
        password: props.password,
        remember: false,
    });
</script>

<template>
    <AuthLayout>
        <Title>
            {{ __('sharp::pages/auth/login.title') }}
        </Title>

        <template #prepend>
            <template v-if="form.hasErrors">
                <Alert class="mb-4" variant="destructive">
                    <AlertTitle>
                        {{ Object.values(form.errors)[0] }}
                    </AlertTitle>
                </Alert>
            </template>
            <template v-if="session('status')">
                <Alert class="mb-4">
                    <Check class="w-4 h-4" />
                    <AlertTitle>
                        {{ session('status') }}
                    </AlertTitle>
                </Alert>
            </template>
        </template>

        <form @submit.prevent="form.post(route('code16.sharp.login.post'))">
            <AuthCard>
                <template #title>
                    Connexion
                </template>
                <template v-if="description" #description>
                    {{ description }}
                </template>
                <div class="grid gap-4">
                    <FormItem>
                        <Label for="login">{{ __('sharp::pages/auth/login.login_field') }}</Label>
                        <Input type="text" name="login" id="login" v-model="form.login" autocomplete="username" autofocus />
                    </FormItem>
                    <FormItem>
                        <div class="flex items-center">
                            <Label for="password">{{ __('sharp::pages/auth/login.password_field') }}</Label>
                            <template v-if="config('sharp.auth.forgotten_password.enabled')">
                                <a :href="route('code16.sharp.password.request')" class="ml-auto inline-block text-sm underline">
                                    {{ __('sharp::pages/auth/login.forgot_password_link') }}
                                </a>
                            </template>
                        </div>
                        <Input id="password" type="password" v-model="form.password" autocomplete="current-password" />
                    </FormItem>
                    <template v-if="config('sharp.auth.login_form.suggest_remember_me')">
                        <FormItem>
                            <div class="flex items-center space-x-2">
                                <Checkbox id="remember_me" :checked="form.remember" @update:checked="form.remember = $event" />
                                <Label for="remember_me">
                                    {{ __('sharp::pages/auth/login.remember') }}
                                </Label>
                            </div>
                        </FormItem>
                    </template>
                </div>
                <template #footer>
                    <Button type="submit" class="w-full">
                        {{ __('sharp::pages/auth/login.button') }}
                    </Button>
                </template>
            </AuthCard>
        </form>

        <template v-if="append" v-slot:append>
            <div v-html="append"></div>
        </template>
    </AuthLayout>
</template>
