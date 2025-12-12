import { route as routeFn } from 'ziggy-js';
import { config } from "@/utils/config";


export function isSharpLink(url: string) {
    return (
            url.startsWith(`${location.origin}/${config('sharp.custom_url_segment')}/`)
            || url.startsWith(`/${config('sharp.custom_url_segment')}/`)
        )
        && !url.includes(`/${config('sharp.custom_url_segment')}/download/`);
}

export const route = routeFn;
