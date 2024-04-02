<script setup lang="ts">
    import AuthLayout from "@/Layouts/Auth/AuthLayout.vue";
    import { useForm } from "@inertiajs/vue3";
    import { config } from "@/utils/config";
    import { __ } from "@/utils/i18n";
    import Title from "@/components/Title.vue";
    import { route } from "@/utils/url";
    import { Button } from "@/components/ui/button";
    import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
    import { Label } from "@/components/ui/label";
    import { Checkbox } from "@/components/ui/checkbox";
    import { Input } from "@/components/ui/input";
    import SharpIcon from "@/svg/SharpIcon.vue";
    import AuthCardHeader from "@/Layouts/Auth/AuthCardHeader.vue";

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
        <template v-if="form.hasErrors" #prepend>
            <div class="rounded-md bg-red-100 p-4 mb-4">
                <h3 class="text-sm font-medium text-red-800">
                    {{ Object.values(form.errors)[0] }}
                </h3>
            </div>
        </template>

        <form @submit.prevent="form.post(route('code16.sharp.login.post'))">
            <Card class="w-full max-w-sm">
                <AuthCardHeader
                    :description="description"
                />
                <CardContent class="grid gap-4">
                    <div class="grid gap-2">
                        <Label for="login">{{ __('sharp::pages/auth/login.login_field') }}</Label>
                        <Input type="text" name="login" id="login" v-model="form.login" autocomplete="username" autofocus />
                    </div>
                    <div class="grid gap-2">
                        <div class="flex items-center">
                            <Label for="password">{{ __('sharp::pages/auth/login.password_field') }}</Label>
                            <template v-if="config('sharp.auth.forgotten_password.enabled')">
                                <a :href="route('code16.sharp.password.request')" class="ml-auto inline-block text-sm underline">
                                    {{ __('sharp::pages/auth/login.forgot_password_link') }}
                                </a>
                            </template>
                        </div>
                        <Input type="password" name="password" id="password" v-model="form.password" autocomplete="current-password" />
                    </div>
                    <template v-if="config('sharp.auth.login_form.suggest_remember_me')">
                        <div class="flex items-center space-x-2">
                            <Checkbox id="remember_me" :checked="form.remember" @update:checked="form.remember = $event" />
                            <Label for="remember_me">
                                {{ __('sharp::pages/auth/login.remember') }}
                            </Label>
                        </div>
                    </template>
                </CardContent>
                <CardFooter>
                    <Button type="submit" class="w-full">
                        {{ __('sharp::pages/auth/login.button') }}
                    </Button>
                </CardFooter>
            </Card>
        </form>

        <template v-if="append" v-slot:append>
            <div v-html="append"></div>
        </template>
    </AuthLayout>
</template>
