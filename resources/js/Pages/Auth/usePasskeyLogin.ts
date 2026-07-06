import { useForm } from "@inertiajs/vue3";
import { api } from "@/api/api";
import { route } from "@/utils/url";
import { browserSupportsWebAuthnAutofill, startAuthentication } from "@simplewebauthn/browser";
import { onMounted } from "vue";

type PasskeyLoginOptions = {
    autofill?: boolean;
}

export function usePasskeyLogin(options: PasskeyLoginOptions) {
    return useSpatiePasskeyLogin(options);
}

function useSpatiePasskeyLogin(options: PasskeyLoginOptions) {
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

            passkeyForm.post(route('passkeys.login'), {
                headers: {
                    'X-Sharp': '1',
                },
            });
        } catch (error) {
            console.error(error);
        }
    }

    if(options.autofill && browserSupportsWebAuthnAutofill()) {
        onMounted(() => {
            loginWithPasskey({ autofill: true });
        })
    }

    return { loginWithPasskey }
}
