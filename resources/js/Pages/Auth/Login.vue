<script setup lang="ts">
    import AuthLayout from "@/Layouts/AuthLayout.vue";
    import { useForm } from "@inertiajs/vue3";
    import { config } from "@/util/config";
    import { __ } from "@/util/i18n";

    const append = document.querySelector('#login-append')?.innerHTML;
    const form = useForm({
        login: '',
        password: '',
        remember: false,
    });
</script>


<template>
    <AuthLayout>
        <template v-if="form.hasErrors">
            <div class="text-red-900 mb-4">
                <template v-for="error in form.errors">
                    <div>
                        {{ error }}
                    </div>
                </template>
            </div>
        </template>

        <form @submit.prevent="form.post(route('code16.sharp.login.post'))">
            <div class="SharpForm__form-item mb-3">
                <input type="text" name="login" id="login" class="form-control"
                    :placeholder="__('sharp::login.login_field')" v-model="form.login" autocomplete="username" autofocus>
            </div>

            <div class="SharpForm__form-item mb-3">
                <input type="password" name="password" id="password" class="form-control"
                    :placeholder="__('sharp::login.password_field')" v-model="form.password" autocomplete="current-password">
            </div>

            <template v-if="config('sharp.auth.suggest_remember_me')">
                <div class="SharpForm__form-item mb-3">
                    <div class="SharpCheck form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember"
                            id="remember" v-model="form.remember">
                        <label class="form-check-label" for="remember">
                            {{ __('sharp::login.remember') }}
                        </label>
                    </div>
                </div>
            </template>

            <div class="text-center mt-4">
                <button type="submit" id="submit" class="btn btn-primary btn-lg">
                    {{ __('sharp::login.button') }}
                </button>
            </div>
        </form>

        <template v-if="append" v-slot:append>
            <div v-html="append"></div>
        </template>
    </AuthLayout>
</template>
