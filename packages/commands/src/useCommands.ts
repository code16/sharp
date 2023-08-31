import { Endpoints, CommandManager, Handlers } from "./CommandManager";
import { ConfigCommandsData } from "@/types";
import { showAlert } from "@/utils/dialogs";
import { __ } from "@/utils/i18n";
import { router } from "@inertiajs/vue3";
import { Ref } from "vue";
import CommandHandler from "./components/CommandHandler.vue";


export function useCommands(
    commands: ConfigCommandsData,
    endpoints: Endpoints,
) {
    return new CommandManager(
        commands,
        endpoints,
    );
}
