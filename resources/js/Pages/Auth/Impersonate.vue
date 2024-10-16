<script setup lang="ts">
    import { useForm } from "@inertiajs/vue3";
    import AuthLayout from "@/Layouts/Auth/AuthLayout.vue";
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import Title from "@/components/Title.vue";
    import { config } from "@/utils/config";
    import { route } from "@/utils/url";
    import AuthCard from "@/Layouts/Auth/AuthCard.vue";
    import { Label } from "@/components/ui/label";
    import { FormItem } from "@/components/ui/form";
    import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

    const props = defineProps<{
        impersonateUsers: { [userId:string]: string },
    }>()

    const form = useForm({
        user_id: Object.keys(props.impersonateUsers ?? {})[0],
    });
</script>

<template>
    <Title>
        {{ __('sharp::pages/auth/impersonate.title') }}
    </Title>
    <AuthLayout>
        <form @submit.prevent="form.post(route('code16.sharp.impersonate.post'))">
            <AuthCard>
                <template #title>
                    {{ __('sharp::pages/auth/impersonate.title') }}
                </template>
                <FormItem>
                    <Label for="user_id">
                        {{ __('sharp::pages/auth/impersonate.user_id_field') }}
                    </Label>
                    <Select id="user_id" v-model="form.user_id">
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <template v-for="(email, userId) in impersonateUsers">
                                    <SelectItem :value="userId">
                                        {{ email }}
                                    </SelectItem>
                                </template>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </FormItem>
                <template #footer>
                    <div class="w-full">
                        <Button class="w-full" type="submit">
                            {{ __('sharp::pages/auth/impersonate.button') }}
                        </Button>

                        <div class="mt-4 text-center">
                            <a class="text-xs underline" :href="route('code16.sharp.login')">
                                {{ __('sharp::pages/auth/impersonate.switch_to_password_login') }}
                            </a>
                        </div>
                    </div>
                </template>
            </AuthCard>
        </form>
    </AuthLayout>
</template>
