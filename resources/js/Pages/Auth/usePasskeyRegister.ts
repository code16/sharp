import { useForm } from "@inertiajs/vue3";
import { api } from "@/api/api";
import { route } from "@/utils/url";
import { startRegistration } from "@simplewebauthn/browser";

export function usePasskeyRegister() {
    return useSpatiePasskeyRegister();
}

function useSpatiePasskeyRegister() {
    const form = useForm({
        name: '',
        passkey: '',
    });

    async function registerPasskey() {
        form.clearErrors();

        try {
            const optionsResponse = await api.post(route('code16.sharp.passkeys.spatie.validate'), {
                name: form.name,
            });

            const registrationResponse = await startRegistration({
                optionsJSON: optionsResponse.data.passkeyOptions,
            });

            form.passkey = JSON.stringify(registrationResponse);

            form.post(route('code16.sharp.passkeys.spatie.store'));
        } catch (error: any) {
            if (error.response?.status === 422) {
                form.setError({ name: error.response.data.errors?.name?.[0] });
            } else {
                console.error(error);
            }
        }
    }

    return { form, registerPasskey }
}
