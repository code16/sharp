import { CommandAction, CommandData, CommandReturnData, ConfigCommandsData } from "@/types";
import { showAlert } from "@/utils/dialogs";
import { __ } from "@/utils/i18n";
import { router } from "@inertiajs/vue3";

type Handlers = {
    [Action in CommandAction]: (data: Extract<CommandReturnData, { action: Action }>) => void
}

export class CommandManager {
    currentCommand: CommandData | null = null;
    defaultHandlers: Partial<Handlers> = {
        info(data) {
            showAlert(data.message, {
                title: __('sharp::modals.command.info.title'),
            });
        },
        link(data) {
            const url = new URL(data.link);
            if(url.origin === location.origin) {
                router.visit(url.pathname + url.search);
            } else {
                location.href = data.link;
            }
        },
    }

    constructor(commands: ConfigCommandsData, handlers: Partial<Handlers>) {

    }
}
