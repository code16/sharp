import { CommandManager } from "./CommandManager";


export function useCommands(...args: ConstructorParameters<typeof CommandManager>) {
    return new CommandManager(...args);
}
