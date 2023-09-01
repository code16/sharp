import { CommandData, CommandReturnData, ConfigCommandsData, FormData } from "@/types";
import { api } from "@/api";
import { showConfirm } from "@/utils/dialogs";
import { parseBlobJSONContent } from "@/utils/request";
import { EventEmitter } from "@/utils/EventEmitter";
import { AxiosResponse } from "axios";

export type Handlers = {
    [Action in CommandReturnData['action']]: (data: Extract<CommandReturnData, { action: Action }>) => void | Promise<void>
}

export type Endpoints = {
    postCommand: (commandKey: CommandData) => string
    getForm: (command: CommandData, query?) => string
}

export class CommandManager extends EventEmitter<any> {
    commands: ConfigCommandsData;
    endpoints: Endpoints;

    constructor(
        commands: ConfigCommandsData,
        endpoints: Endpoints,
    ) {
        super();
        this.commands = commands;
        this.endpoints = endpoints;
    }

    async send(command: CommandData) {
        if(command.has_form) {
            const form = await api.get(this.endpoints.getForm(command)).then(r => r.data);
            this.emit('showForm', command, form);
            return;
        }

        if(command.confirmation) {
            if(! await showConfirm(command.confirmation)) {
                return;
            }
        }

        const response = await api.post(this.endpoints.postCommand(command)).then(r => r.data);

        this.handleCommandResponse(response);
    }

    async handleCommandResponse(response: AxiosResponse) {
        if(response.data.type !== 'application/json') {
            location.replace(URL.createObjectURL(response.data));
            return null;
        }

        const data = await parseBlobJSONContent(response.data);
        await this.handleCommandReturn(data);
        return data;
    }

    handleCommandReturn(data: CommandReturnData): void | Promise<void> {
        this.emit('commandReturn', data);
    }
}
