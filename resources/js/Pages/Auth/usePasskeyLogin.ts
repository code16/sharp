import { useForm } from "@inertiajs/vue3";
import { api } from "@/api/api";
import { route } from "@/utils/url";
import { startAuthentication } from "@simplewebauthn/browser";

export function usePasskeyLogin() {
    const passkeyForm = useForm({
        remember: false,
        start_authentication_response: '',
    });

    async function loginWithPasskey({ autofill = false, remember = false }) {
        try {
            const response = await api.get(route('passkeys.authentication_options'), {
                ignoreContentType: true,
            });

            const authenticationOptions = response.data;
            const authenticationResponse = await startAuthentication({
                optionsJSON: authenticationOptions,
                useBrowserAutofill: autofill,
            });

            passkeyForm.remember = remember;
            passkeyForm.start_authentication_response = JSON.stringify(authenticationResponse);

            passkeyForm.post(route('passkeys.login'));
        } catch (error) {
            console.error(error);
        }
    }

    return { loginWithPasskey }
}
