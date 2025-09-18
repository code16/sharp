import { Extension, textInputRule } from "@tiptap/core";
import { FormEditorFieldData } from "@/types";


export function getTextInputReplacementsExtension(field: FormEditorFieldData, locale: string) {
    return Extension.create({
        name: 'textInputReplacements',
        addInputRules() {
            return field.textInputReplacements
                .filter(replacement => !replacement.locale || replacement.locale === locale)
                .map(replacement => {
                    const pattern = replacement.pattern.replace(/^\//, '').replace(/\/$/, '').replace(/\$?$/, '$');
                    try {
                        return textInputRule({
                            find: new RegExp(pattern, 'u'),
                            replace: replacement.replacement,
                        });
                    } catch (e) {
                        console.error(e);
                    }
                    return null;
                })
                .filter(Boolean);
        },
    });
}
