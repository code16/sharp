import { getXsrfToken, lang, filesizeLabel } from "sharp";

export const defaultUploadOptions = {
    headers: {
        'X-XSRF-TOKEN': getXsrfToken(),
    },
}

// size in Mo
export function maxFileSizeMessage(size) {
    const bytes = size * 1024 * 1024;
    return lang('form.upload.message.file_too_big')
        .replace(':size', filesizeLabel(bytes));
}
