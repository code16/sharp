import { CommandData, CommandReturnData, ConfigCommandsData, FormData } from "@/types";
import { AxiosResponse } from "axios";
import { api } from "@/api";
import { showConfirm } from "@/utils/dialogs";
import { parseBlobJSONContent } from "@/utils/request";

export type Handlers = {
    [Action in CommandReturnData['action']]: (data: Extract<CommandReturnData, { action: Action }>) => void | Promise<void>
}

export type Endpoints = {
    postCommand: (commandKey: CommandData) => string
    getForm: (command: CommandData, query?) => string
}

export class CommandManager {
    commands: ConfigCommandsData;
    endpoints: Endpoints;
    currentCommand: CommandData | null = null;
    currentCommandForm: FormData | null = null;
    currentCommandReturn: CommandReturnData | null = null;

    constructor(
        commands: ConfigCommandsData,
        endpoints: Endpoints,
        onCommandReturn?: (data: CommandReturnData) => void
    ) {
        this.commands = commands;
        this.endpoints = endpoints;
    }

    async send(command: CommandData) {
        if(command.has_form) {
            this.currentCommand = command;
            this.currentCommandForm = await api.get(this.endpoints.getForm(command)).then(r => r.data);
            return;
        }

        if(command.confirmation) {
            if(! await showConfirm(command.confirmation)) {
                return;
            }
        }

        const response = await api.post(this.endpoints.postCommand(command)).then(r => r.data);
        if(response.data.type !== 'application/json') {
            location.replace(URL.createObjectURL(response.data));
            return null;
        }

        const data = await parseBlobJSONContent(response.data);
        await this.handleCommandReturn(data);
        return data;
    }

    // withCommandReturnHandlers(handlers: Partial<Handlers>) {
    //     Object.assign(this.handlers, handlers);
    // }
    //
    handleCommandReturn(data: CommandReturnData): void | Promise<void> {
        return this.handlers[data.action]?.(data as any);
    }
}
