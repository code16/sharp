import { session } from "@/utils/session";

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
    return session('_token');
}
