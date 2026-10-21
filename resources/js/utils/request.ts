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

/**
 * Compare only the MIME type, as Blob.type may include parameters (e.g. "application/json; charset=utf-8")
 */
export function blobHasMimeType(blob: Blob, mimeType: string) {
    return blob.type.split(';')[0].trim().toLowerCase() === mimeType;
}

export function getCsrfToken() {
    return session('_token');
}
