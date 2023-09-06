import { CommandData, CommandReturnData } from "@/types";

export type CommandReturnHandlers = {
    [Action in CommandReturnData['action']]: (data: Extract<CommandReturnData, { action: Action }>) => void | Promise<void>
}

export type GetFormQuery = {
    command_step: string,
};

export type CommandEndpoints = {
    postCommand: string
    getForm: string
    query?: object
}
