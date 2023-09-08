import { CommandManager } from "./CommandManager";
import { CommandReturnHandlers } from "./types";


export function useCommands(commandReturnHandlers?: Partial<CommandReturnHandlers>) {
    return new CommandManager(commandReturnHandlers);
}
