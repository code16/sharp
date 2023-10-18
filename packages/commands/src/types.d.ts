import { CommandResponseData } from "@/types";

export type CommandResponseHandlers = {
    [Action in CommandResponseData['action']]: (data: Extract<CommandResponseData, { action: Action }>) => void | Promise<void>
}

export type GetFormQuery = {
    command_step: string,
};

export type CommandEndpoints = {
    postCommand: string
    getForm: string
    query?: object

    entityKey: string
    instanceId?: string | number
}
