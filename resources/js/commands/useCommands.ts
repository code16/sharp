import { CommandManager } from "./CommandManager";
import { CommandResponseHandlers } from "./types";


export function useCommands(commandResponseHandlers?: Partial<CommandResponseHandlers>) {
    return new CommandManager(commandResponseHandlers);
}
