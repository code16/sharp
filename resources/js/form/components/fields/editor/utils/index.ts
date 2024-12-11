import { FormEditorFieldData, FormEditorToolbarButton } from "@/types";


export function getAllowedHeadingLevels(toolbar) {
    return toolbar
        .map(button => button.match(/^heading-(\d)$/))
        .filter(match => !!match)
        .map(match => Number(match[1]));
}

export function toolbarHasButton(field: FormEditorFieldData, buttonName: FormEditorToolbarButton | FormEditorToolbarButton[]) {
    return !field.toolbar || field.toolbar.some(button =>
        Array.isArray(buttonName)
            ? buttonName.includes(button)
            : button === buttonName
    );
}
