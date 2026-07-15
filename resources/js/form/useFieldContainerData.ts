import { RequestFieldContainerData } from "@/types";
import { useParentCommands } from "@/commands/useCommands";
import { Form } from "@/form/Form";
import { useParentEditor } from "@/form/components/fields/editor/useParentEditor";


export function useFieldContainerData(form: Form): RequestFieldContainerData {
    const parentCommands = useParentCommands();
    const parentEditor = useParentEditor();

    return {
        embed_key: form.embedKey,
        embed_editor_key: parentEditor?.props.field.key,
        entity_list_command_key: parentCommands?.commandContainer === 'entityList' ? form.commandKey : null,
        show_command_key: parentCommands?.commandContainer === 'show' ? form.commandKey : null,
        dashboard_command_key: parentCommands?.commandContainer === 'dashboard' ? form.commandKey : null,
        instance_id: form.instanceId,
    };
}
