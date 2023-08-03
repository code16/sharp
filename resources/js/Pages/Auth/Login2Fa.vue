<script setup lang="ts">
    import AuthLayout from "@/Layouts/AuthLayout.vue";
    import { useForm } from "@inertiajs/vue3";
    import { __ } from "@/util/i18n";

    defineProps<{
        helpText: string
    }>();

    const form = useForm({
        code: "",
    });
</script>

<template>
    <AuthLayout>
        <template v-if="form.hasErrors" #prepend>
            <div class="rounded-md bg-red-100 p-4 mb-4">
                <h3 class="text-sm font-medium text-red-800">
                    {{ Object.values(form.errors)[0] }}
                </h3>
            </div>
        </template>

        <div class="mb-3" v-html="helpText">
        </div>

        <form @submit.prevent="form.post(route('code16.sharp.login.2fa.post'))">
            <div class="mb-3">
                <input type="text" name="code" id="code" class="form-control" v-model="form.code" :placeholder="__('sharp::login.code_field')">
            </div>

            <div class="text-center mt-4">
                <button type="submit" id="submit" class="btn btn-primary btn-lg">
                    {{ __('sharp::login.button') }}
                </button>
            </div>
        </form>
    </AuthLayout>
</template>
