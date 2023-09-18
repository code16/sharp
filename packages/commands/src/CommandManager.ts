import { CommandData, CommandFormData, CommandReturnData, FormData } from "@/types";
import { api } from "@/api";
import { showAlert, showConfirm } from "@/utils/dialogs";
import { parseBlobJSONContent } from "@/utils/request";
import { AxiosResponse } from "axios";
import { reactive } from "vue";
import { __ } from "@/utils/i18n";
import { router } from "@inertiajs/vue3";
import { CommandReturnHandlers, CommandEndpoints, GetFormQuery } from "./types";


export class CommandManager {
    commandReturnHandlers: CommandReturnHandlers;

    state = reactive<{
        currentCommand?: CommandData,
        currentCommandForm?: CommandFormData,
        currentCommandFormLoading?: boolean,
        currentCommandReturn?: CommandReturnData,
        currentCommandEndpoints?: CommandEndpoints,
    }>({});

    constructor(commandReturnHandlers?: Partial<CommandReturnHandlers>) {
        this.commandReturnHandlers = {
            ...this.defaultCommandReturnHandlers,
            ...commandReturnHandlers,
        }
    }

    get defaultCommandReturnHandlers(): CommandReturnHandlers {
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
                this.state.currentCommandReturn = data;
                this.state.currentCommandForm = await this.getForm({
                    command_step: data.step,
                });
            },
            view: (data) => {
                this.state.currentCommandReturn = data;
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

        await this.handleCommandResponse(response);
    }

    finish() {
        this.state.currentCommand = null;
        this.state.currentCommandForm = null;
        this.state.currentCommandFormLoading = false;
        this.state.currentCommandReturn = null;
        this.state.currentCommandEndpoints = null;
    }

    async handleCommandResponse(response: AxiosResponse): Promise<CommandReturnData | null> {
        if(response.data.type !== 'application/json') {
            location.replace(URL.createObjectURL(response.data)); // download the file
            return null;
        }

        const data = await parseBlobJSONContent(response.data);

        await this.handleCommandReturn(data);
    }

    handleCommandReturn(data: CommandReturnData): void | Promise<void> {
        return this.commandReturnHandlers[data.action]?.(data as any);
    }

    getForm(query?: GetFormQuery): Promise<CommandFormData> {
        this.state.currentCommandFormLoading = true;

        return api.get(this.state.currentCommandEndpoints.getForm, {
            params: {
                ...query,
                ...this.state.currentCommandEndpoints.query
            }
        })
            .then(response => response.data)
            .finally(() => {
                this.state.currentCommandFormLoading = false;
            });
    }

    async postForm(data: CommandFormData['data']) {
        const response = await api.post(this.state.currentCommandEndpoints.postCommand, {
            data,
            query: this.state.currentCommandEndpoints.query,
            command_step: this.state.currentCommandReturn?.action === 'step'
                ? this.state.currentCommandReturn.step
                : null,
        }, {
            responseType: 'blob',
        });

        this.handleCommandResponse(response);
    }
}
