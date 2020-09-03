import { getXsrfToken } from "sharp";

export const defaultUploadOptions = {
    headers: {
        'X-XSRF-TOKEN': getXsrfToken(),
    },
}