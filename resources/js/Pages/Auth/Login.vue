<script setup lang="ts">
    import AuthLayout from "@/Layouts/AuthLayout.vue";
    import { useForm } from "@inertiajs/vue3";
    import { config } from "@/utils/config";
    import { __ } from "@/utils/i18n";
    import Title from "@/components/Title.vue";
    import { route } from "@/utils/url";

    const append = document.querySelector('#login-append')?.innerHTML;
    const form = useForm({
        login: '',
        password: '',
        remember: false,
    });
</script>


<template>
    <AuthLayout :show-site-name="config('sharp.auth.login_form.display_app_name')">
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
            <div class="mb-3">
                <input type="text" name="login" id="login" class="form-control"
                    :placeholder="__('sharp::pages/auth/login.login_field')" v-model="form.login" autocomplete="username" autofocus>
            </div>

            <div class="mb-3">
                <input type="password" name="password" id="password" class="form-control"
                    :placeholder="__('sharp::pages/auth/login.password_field')" v-model="form.password" autocomplete="current-password">
            </div>

            <template v-if="config('sharp.auth.forgotten_password.enabled')">
                <div class="mb-3 -mt-2">
                    <a class="font-medium text-xs text-primary-600 hover:text-primary-500" :href="route('code16.sharp.password.request')">
                        {{ __('sharp::pages/auth/login.forgot_password_link') }}
                    </a>
                </div>
            </template>

            <template v-if="config('sharp.auth.suggest_remember_me')">
                <div class="SharpForm__form-item mb-3">
                    <div class="SharpCheck form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember"
                            id="remember" v-model="form.remember">
                        <label class="form-check-label" for="remember">
                            {{ __('sharp::pages/auth/login.remember') }}
                        </label>
                    </div>
                </div>
            </template>

            <div class="text-center mt-4">
                <button type="submit" id="submit" class="btn btn-primary btn-lg w-full">
                    {{ __('sharp::pages/auth/login.button') }}
                </button>
            </div>
        </form>

        <template v-if="append" v-slot:append>
            <div v-html="append"></div>
        </template>
    </AuthLayout>
</template>
