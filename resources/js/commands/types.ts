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
    /**
     * Called after the form is submitted and a successful response is received with some exceptions:
     * - if reopen has been clicked, it is not called
     * - if the response is a wizard step, it is not called
     */
    onSuccess?: () => void

    entityKey: string
    instanceId?: string | number
}
