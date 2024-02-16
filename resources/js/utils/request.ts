import { usePage } from "@inertiajs/vue3";

export function parseBlobJSONContent(blob) {
    return new Promise(resolve => {
        let reader = new FileReader();
        reader.addEventListener("loadend", function() {
            resolve(JSON.parse(reader.result as string));
        });
        reader.readAsText(blob);
    });
}

export function getCsrfToken() {
    return usePage().props.session.token;
}
