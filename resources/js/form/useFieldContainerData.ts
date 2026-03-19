import { RequestFieldContainerData } from "@/types";
import { useParentCommands } from "@/commands/useCommands";
import { Form } from "@/form/Form";


export function useFieldContainerData(form: Form): RequestFieldContainerData {
    const parentCommands = useParentCommands();

    return {
        embed_key: form.embedKey,
        entity_list_command_key: parentCommands?.commandContainer === 'entityList' ? form.commandKey : null,
        show_command_key: parentCommands?.commandContainer === 'show' ? form.commandKey : null,
        dashboard_command_key: parentCommands?.commandContainer === 'dashboard' ? form.commandKey : null,
        instance_id: form.instanceId,
    };
}
