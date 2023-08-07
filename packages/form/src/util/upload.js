import { getXsrfToken, filesizeLabel, UPLOAD_URL } from "sharp";
import { __ } from "@/util/i18n";

// size in Mo
export function maxFileSizeMessage(size) {
    const bytes = size * 1024 * 1024;
    return __('sharp::form.upload.message.file_too_big', {
        size: filesizeLabel(bytes)
    });
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
                message: __('sharp::form.upload.message.bad_extension')
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

