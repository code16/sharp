import { CommandResponseData } from "@/types";
import { CommandManager } from "@/commands/CommandManager";

export type CommandContainer = 'dashboard' | 'entityList' | 'show';

export type CommandResponseHandlers = {
    [Action in CommandResponseData['action']]: (
        data: Extract<CommandResponseData, { action: Action }>,
        context: ReturnType<CommandManager['getCommandResponseHandlerContext']>
    ) => void | Promise<void>
}

export type CommandFormExtraData = {
    _shouldReopen?: boolean
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
