import { RequestFieldContainerData } from "@/types";
import { useParentForm } from "@/form/useParentForm";
import { useParentCommands } from "@/commands/useCommands";


export function useFieldContainerData(): RequestFieldContainerData {
    const form = useParentForm();
    const parentCommands = useParentCommands();

    return {
        embed_key: form.embedKey,
        entity_list_command_key: parentCommands?.commandContainer === 'entityList' ? form.commandKey : null,
        show_command_key: parentCommands?.commandContainer === 'show' ? form.commandKey : null,
        dashboard_command_key: parentCommands?.commandContainer === 'dashboard' ? form.commandKey : null,
        instance_id: form.instanceId,
    };
}
