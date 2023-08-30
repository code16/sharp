import { CommandAction, CommandData, CommandReturnData, ConfigCommandsData } from "@/types";

export type Handlers = {
    [Action in CommandAction]: (data: Extract<CommandReturnData, { action: Action }>) => void
}

export class CommandManager {
    commands: ConfigCommandsData;
    handlers: Handlers;
    currentCommand: CommandData | null = null;
    currentCommandReturn: CommandReturnData | null = null;

    constructor(commands: ConfigCommandsData, handlers: Handlers) {
        this.commands = commands;
        this.handlers = handlers;
    }
}
