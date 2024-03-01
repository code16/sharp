import { FormUploadFieldValueData } from "@/types";


export function parseAttributeValue(value) {
    try {
        return JSON.parse(value);
    } catch {
        return value;
    }
}

export function serializeAttributeValue(value) {
    if(value && typeof value === 'object') {
        return JSON.stringify(value);
    }

    return value;
}

export function serializeUploadAttributeValue(value: FormUploadFieldValueData | null) {
    if(value && typeof value === 'object') {
        const {
            uploaded,
            transformed,
            not_found,
            thumbnail,
            ...serializable
        } = value;

        return JSON.stringify(serializable);
    }

    return value;
}
