import { onBeforeUnmount, onMounted } from "vue";
import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";


export function useEditorNode({ onAdded, onRemoved }: { onAdded: () => void, onRemoved: () => void }) {
    const parentEditor = useParentEditor();
    const locale = parentEditor.props.locale;

    // let localeChanged = false;
    // watch(() => parentEditor.props.locale, () => {
    //     localeChanged = true;
    // }, { flush: 'sync' });

    onMounted(() => {
        onAdded();
    });
    onBeforeUnmount(() => {
        if(!parentEditor.props.field.localized || locale === parentEditor.props.locale) {
            onRemoved();
        }
    });
}
