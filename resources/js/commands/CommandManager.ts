import { CommandData, CommandFormData, CommandResponseData, FormData } from "@/types";
import { api } from "@/api/api";
import { RootAlertDialog, showAlert, showConfirm } from "@/utils/dialogs";
import { parseBlobJSONContent } from "@/utils/request";
import { AxiosResponse } from "axios";
import { reactive } from "vue";
import { __ } from "@/utils/i18n";
import { router } from "@inertiajs/vue3";
import {
    CommandResponseHandlers,
    CommandEndpoints,
    GetFormQuery,
    CommandContainer,
    CommandFormExtraData
} from "./types";
import { Form } from "@/form/Form";
import { isSharpLink } from "@/utils/url";


export class CommandManager {
    commandContainer: CommandContainer;
    commandResponseHandlers: CommandResponseHandlers;

    state = reactive({}) as {
        currentCommand?: CommandData,
        currentCommandForm?: Form,
        currentCommandFormLoading?: boolean,
        currentCommandResponse?: CommandResponseData,
        currentCommandEndpoints?: CommandEndpoints,
        currentCommandShouldReopen?: boolean,
    };

    constructor(commandContainer: CommandContainer, commandResponseHandlers?: Partial<CommandResponseHandlers>) {
        this.commandContainer = commandContainer;
        this.commandResponseHandlers = {
            ...this.defaultCommandResponseHandlers,
            ...commandResponseHandlers,
        }
        this.maybeReopenFromSessionStorage();
    }

    get defaultCommandResponseHandlers(): CommandResponseHandlers {
        return {
            info: async (data, { formModal }) => {
                await showAlert(data.message, {
                    title: __('sharp::modals.command.info.title'),
                });
                if(formModal.shouldReopen) {
                    formModal.reloadAndReopen();
                } else if(data.reload) {
                    await this.handleCommandResponse({ action: 'reload' });
                }
            },
            link: (data, { formModal }) => {
                if(formModal.shouldReopen) {
                    formModal.reloadAndReopen();
                    return;
                }
                if(data.openInNewTab) {
                    window.open(data.link, '_blank');
                } else if(isSharpLink(data.link)) {
                    router.visit(data.link);
                } else {
                    location.href = data.link;
                }
            },
            reload: (data, { formModal }) => {
                return new Promise((resolve) =>
                    router.visit(location.href, {
                        preserveState: false,
                        preserveScroll: true,
                        onStart: () => formModal.shouldReopen && formModal.queueReopen(),
                        onFinish: () => resolve(),
                    })
                );
            },
            refresh: (data, { formModal }) => {
                return new Promise((resolve) =>
                    router.visit(location.href, {
                        preserveState: false,
                        preserveScroll: true,
                        onStart: () => formModal.shouldReopen && formModal.queueReopen(),
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
            view: (data, { formModal }) => {
                if(formModal.shouldReopen) {
                    formModal.reloadAndReopen();
                    return;
                }
                this.state.currentCommandResponse = data;
            },
        };
    }

    async send(command: CommandData, endpoints: CommandEndpoints, confirmDialogOptions?: Partial<RootAlertDialog>) {
        this.finish();
        this.state.currentCommand = command;
        this.state.currentCommandEndpoints = endpoints;

        if(command.hasForm) {
            this.state.currentCommandForm = await this.getForm();
            return;
        }

        if(command.confirmation) {
            if(! await showConfirm(command.confirmation.text, {
                title: command.confirmation.title,
                okTitle: command.confirmation.buttonLabel,
                ...confirmDialogOptions,
            })) {
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
        this.state.currentCommandShouldReopen = false;
    }

    async handleCommandApiResponse(response: AxiosResponse<Blob>): Promise<void> {
        if(response.data.type !== 'application/json') {
            const link = document.createElement("a");
            link.download = (response.headers['content-disposition'] as string)?.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)?.[1] ?? 'download';
            link.href = URL.createObjectURL(response.data);
            link.click();
            if(this.state.currentCommandShouldReopen) {
                await this.reopenCurrentCommand();
                return;
            }
            this.state.currentCommandEndpoints.onSuccess?.();
            this.finish();
            return;
        }

        const data = await parseBlobJSONContent(response.data) as CommandResponseData;

        if(data.action !== 'step') {
            // close form modal
            this.state.currentCommandForm = null;
            if(!this.state.currentCommandShouldReopen) {
                this.state.currentCommandEndpoints.onSuccess?.();
            }
        }

        await this.handleCommandResponse(data);
    }

    getCommandResponseHandlerContext() {
        return {
            formModal: {
                shouldReopen: this.state.currentCommandShouldReopen,
                queueReopen: () => {
                    this.queueReopenInSessionStorage();
                },
                reopen: () => {
                    this.reopenCurrentCommand();
                },
                reloadAndReopen: () => {
                    this.handleCommandResponse({ action: 'reload' });
                },
            },
        }
    }

    handleCommandResponse(data: CommandResponseData): void | Promise<void> {
        return this.commandResponseHandlers[data.action]?.(data as any, this.getCommandResponseHandlerContext());
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
                    this.state.currentCommandEndpoints.instanceId,
                    { commandKey: this.state.currentCommand.key }
                )
            )
            .finally(() => {
                this.state.currentCommandFormLoading = false;
            });
    }

    async postForm(data: CommandFormData['data'] & CommandFormExtraData) {

        if(this.state.currentCommand.confirmation) {
            if(! await showConfirm(this.state.currentCommand.confirmation.text)) {
                return;
            }
        }

        this.state.currentCommandFormLoading = true;
        this.state.currentCommandShouldReopen = data._shouldReopen ?? false;

        try {
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
        } finally {
            this.state.currentCommandFormLoading = false;
        }
    }

    async reopenCurrentCommand() {
        this.state.currentCommandShouldReopen = false;
        this.state.currentCommandForm = await this.getForm();
    }

    queueReopenInSessionStorage() {
        if(this.state.currentCommandShouldReopen) {
            const data = {
                command: this.state.currentCommand,
                commandEndpoints: this.state.currentCommandEndpoints,
                url: location.href,
            };
            sessionStorage.setItem('reopen-command', JSON.stringify(data));
            return data;
        }
    }

    maybeReopenFromSessionStorage() {
        const reopenData: ReturnType<typeof this.queueReopenInSessionStorage> = JSON.parse(
            sessionStorage.getItem('reopen-command') ?? 'null'
        );
        if(reopenData) {
            if(location.href === reopenData.url) {
                this.send(reopenData.command, reopenData.commandEndpoints);
            }
            sessionStorage.removeItem('reopen-command');
        }
    }
}
