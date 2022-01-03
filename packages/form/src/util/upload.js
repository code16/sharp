import { getXsrfToken, lang, filesizeLabel, UPLOAD_URL } from "sharp";


// size in Mo
export function maxFileSizeMessage(size) {
    const bytes = size * 1024 * 1024;
    return lang('form.upload.message.file_too_big')
        .replace(':size', filesizeLabel(bytes));
}


export function getUploadOptions({ fileFilter, maxFileSize }) {
    return {
        url: UPLOAD_URL,
        uploadMultiple: false,
        headers: {
            'X-XSRF-TOKEN': getXsrfToken(),
        },

        ...fileFilter ? {
            acceptedFiles: {
                extensions: fileFilter,
                message: lang('form.upload.message.bad_extension')
            }
        } : null,

        ...maxFileSize ? {
            maxFilesize: {
                limit: maxFileSize,
                message: maxFileSizeMessage(maxFileSize),
            },
        } : null,

        createImageThumbnails: false,
    }
}

