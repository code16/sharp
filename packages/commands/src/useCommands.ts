import { CommandManager, Handlers } from "./CommandManager";
import { ConfigCommandsData } from "@/types";
import { showAlert } from "@/utils/dialogs";
import { __ } from "@/utils/i18n";
import { router } from "@inertiajs/vue3";


export function useCommands(commands: ConfigCommandsData, handlers: Partial<Handlers>) {
    return new CommandManager(commands, {
        info({ message }) {
            showAlert(message, {
                title: __('sharp::modals.command.info.title'),
            });
        },
        link({ link }) {
            const url = new URL(link);
            if(url.origin === location.origin) {
                router.visit(url.pathname + url.search);
            } else {
                location.href = link;
            }
        },
        reload() {
            router.reload();
        },
        ...handlers,
    });
}
