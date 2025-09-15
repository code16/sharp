import { FormUploadFieldValueData } from "@/types";

export type MaybeLocalizedContent = {
    [locale: string]: string | null
} | string | null;

export type FormEditorUploadData = {
    file: FormUploadFieldValueData,
    legend?: string,
    _locale?: string | null,
}

export type FormattedContent<Content extends MaybeLocalizedContent> = Content & { formatted:true };
