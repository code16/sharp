import { CommandManager } from "./CommandManager";
import { CommandContainer, CommandResponseHandlers } from "./types";
import { inject, provide } from "vue";


export function useCommands(commandContainer: CommandContainer, commandResponseHandlers?: Partial<CommandResponseHandlers>) {
    const commandManager = new CommandManager(commandContainer, commandResponseHandlers);

    provide('commandManager', commandManager);

    return commandManager;
}

export function useParentCommands(): CommandManager | null {
    return inject<CommandManager>('commandManager', null);
}
