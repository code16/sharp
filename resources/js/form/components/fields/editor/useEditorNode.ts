import { onBeforeUnmount, onMounted } from "vue";
import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";
import { ExtensionNodeProps } from "@/form/components/fields/editor/types";


export function useEditorNode(
    props: ExtensionNodeProps<any, any>,
    { onAdded, onRemoved }: { onAdded: () => void, onRemoved: () => void }
) {
    const parentEditor = useParentEditor();
    const locale = parentEditor.props.locale;

    // let localeChanged = false;
    // watch(() => parentEditor.props.locale, () => {
    //     localeChanged = true;
    // }, { flush: 'sync' });

    onMounted(() => {
        if(parentEditor.isMounted.value) {
            onAdded();
        }
    });
    onBeforeUnmount(() => {
        if(!parentEditor.isUnmounting.value && (!parentEditor.props.field.localized || locale === parentEditor.props.locale)) {
            onRemoved();
        }
    });
}
