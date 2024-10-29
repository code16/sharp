import routeFn from 'ziggy-js';
import { config } from "@/utils/config";

export function getAppendableParentUri() {
    return location.pathname.replace(new RegExp(`^/${config('sharp.custom_url_segment')}/`), '');
}

export function isSharpLink(url: string) {
    return url.startsWith(`${location.origin}/${config('sharp.custom_url_segment')}/`)
        || url.startsWith(`/${config('sharp.custom_url_segment')}/`);
}

export const route = routeFn;
