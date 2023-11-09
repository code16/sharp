import { CommandData, CommandFormData, CommandResponseData, FormData } from "@/types";
import { api } from "@/api";
import { showAlert, showConfirm } from "@/utils/dialogs";
import { parseBlobJSONContent } from "@/utils/request";
import { AxiosResponse } from "axios";
import { reactive } from "vue";
import { __ } from "@/utils/i18n";
import { router } from "@inertiajs/vue3";
import { CommandResponseHandlers, CommandEndpoints, GetFormQuery } from "./types";
import { Form } from "@sharp/form/src/Form";


export class CommandManager {
    commandResponseHandlers: CommandResponseHandlers;

    state = reactive<{
        currentCommand?: CommandData,
        currentCommandForm?: Form,
        currentCommandFormLoading?: boolean,
        currentCommandResponse?: CommandResponseData,
        currentCommandEndpoints?: CommandEndpoints,
    }>({});

    constructor(commandResponseHandlers?: Partial<CommandResponseHandlers>) {
        this.commandResponseHandlers = {
            ...this.defaultCommandResponseHandlers,
            ...commandResponseHandlers,
        }
    }

    get defaultCommandResponseHandlers(): CommandResponseHandlers {
        return {
            info: async ({ message }) => {
                await showAlert(message, {
                    title: __('sharp::modals.command.info.title'),
                });
                this.finish();
            },
            link: ({ link }) => {
                const url = new URL(link);
                if(url.origin === location.origin) {
                    router.visit(url.pathname + url.search);
                } else {
                    location.href = link;
                }
            },
            reload: () => {
                return new Promise((resolve) =>
                    router.visit(location.href, {
                        preserveState: false,
                        preserveScroll: false,
                        onFinish: () => resolve(),
                    })
                );
            },
            refresh: () => {
                return new Promise((resolve) =>
                    router.visit(location.href, {
                        preserveState: false,
                        preserveScroll: false,
                        onFinish: () => resolve(),
                    })
                );
            },
            step: async (data) => {
                this.state.currentCommandResponse = data;
                this.state.currentCommandForm = await this.getForm({
                    command_step: data.step,
                });
            },
            view: (data) => {
                this.state.currentCommandResponse = data;
            },
        };
    }

    async send(command: CommandData, endpoints: CommandEndpoints) {
        this.state.currentCommand = command;
        this.state.currentCommandEndpoints = endpoints;

        if(command.has_form) {
            this.state.currentCommandForm = await this.getForm();
            return;
        }

        if(command.confirmation) {
            if(! await showConfirm(command.confirmation)) {
                this.finish();
                return;
            }
        }

        const response = await api.post(endpoints.postCommand, {
            query: endpoints.query
        }, {
            responseType: 'blob'
        });

        await this.handleCommandApiResponse(response);
    }

    finish() {
        this.state.currentCommand = null;
        this.state.currentCommandForm = null;
        this.state.currentCommandFormLoading = false;
        this.state.currentCommandResponse = null;
        this.state.currentCommandEndpoints = null;
    }

    async handleCommandApiResponse(response: AxiosResponse<Blob>): Promise<void> {
        if(response.data.type !== 'application/json') {
            location.replace(URL.createObjectURL(response.data)); // download the file
            return;
        }

        const data = await parseBlobJSONContent(response.data);

        await this.handleCommandResponse(data as CommandResponseData);
    }

    handleCommandResponse(data: CommandResponseData): void | Promise<void> {
        return this.commandResponseHandlers[data.action]?.(data as any);
    }

    getForm(query?: GetFormQuery): Promise<Form> {
        this.state.currentCommandFormLoading = true;

        return api.get(this.state.currentCommandEndpoints.getForm, {
            params: {
                ...query,
                ...this.state.currentCommandEndpoints.query
            }
        })
            .then(response =>
                new Form(
                    response.data,
                    this.state.currentCommandEndpoints.entityKey,
                    this.state.currentCommandEndpoints.instanceId
                )
            )
            .finally(() => {
                this.state.currentCommandFormLoading = false;
            });
    }

    async postForm(data: CommandFormData['data']) {
        const response = await api.post(this.state.currentCommandEndpoints.postCommand, {
            data,
            query: this.state.currentCommandEndpoints.query,
            command_step: this.state.currentCommandResponse?.action === 'step'
                ? this.state.currentCommandResponse.step
                : null,
        }, {
            responseType: 'blob',
        });

        this.handleCommandApiResponse(response);
    }
}
