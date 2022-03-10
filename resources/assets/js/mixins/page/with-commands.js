import { parseBlobJSONContent, getFileName } from "../../util/request";
import { lang, withLoadingOverlay } from "../../index";
import { showConfirm, showAlert } from "../../util/dialogs";

export default {
    data() {
        return {
            currentCommand: null,
            currentCommandStep: null,
            commandViewContent: null,
            commandEndpoints: {
                postCommand: null,
                getForm: null,
            },
        }
    },
    methods: {
        transformCommandForm(form) {
            return {
                ...form,
                layout: { tabs: [{ columns: [{fields:form.layout}] }] }
            };
        },
        downloadCommandFile(response) {
            let $link = document.createElement('a');
            this.$el.appendChild($link);
            $link.href = URL.createObjectURL(response.data);
            $link.download = getFileName(response.headers);
            $link.click();
        },
        async handleCommandResponse(response) {
            if(response.data.type !== 'application/json') {
                this.downloadCommandFile(response);
                return null;
            }
            const data = await parseBlobJSONContent(response.data);
            this.handleCommandActionRequested(data.action, data);
            return data;
        },
        async postCommandForm() {
            const { postCommand } = this.commandEndpoints;
            const response = await this.$refs.commandForm.submit({
                postFn: data => postCommand({ data, command_step: this.currentCommandStep }),
            });
            const data = await this.handleCommandResponse(response);
            if(data?.action === 'step') {
                this.currentCommandStep = data.step;
                await this.showCommandForm(this.currentCommand);
            } else {
                this.currentCommand = null;
            }
        },
        async showCommandForm(command) {
            const { getForm } = this.commandEndpoints;

            if(command.has_form) {
                const form = await withLoadingOverlay(getForm({
                    command_step: this.currentCommandStep,
                }));
                this.currentCommand = {
                    ...command,
                    form: this.transformCommandForm(form),
                };
            }
        },
        async sendCommand(command, { postCommand, getForm }) {
            this.commandEndpoints = { postCommand, getForm };

            if(command.has_form) {
                return this.showCommandForm(command);
            }

            if(command.confirmation) {
                await new Promise(resolve => {
                    showConfirm(command.confirmation, {
                        title: lang('modals.command.confirm.title'),
                        okCallback: resolve,
                    });
                });
            }

            try {
                let response = await withLoadingOverlay(postCommand());
                await this.handleCommandResponse(response);
            } catch(e) {
                console.error(e);
            }
        },

        /** mixin API */
        addCommandActionHandlers(handlers) {
            this.commandHandlers = {
                ...this.commandHandlers,
                ...handlers,
            };
        },
        handleCommandActionRequested(action, data) {
            const handler = this.commandHandlers[action];

            if(handler) {
                handler(data);
            }
        },

        /** Command actions handlers */
        handleReloadCommand() {
            this.init();
        },
        handleInfoCommand(data) {
            showAlert(data.message, {
                title: lang('modals.command.info.title'),
            });
        },
        handleViewCommand(data) {
            this.commandViewContent = data.html;
        },
        handleLinkCommand(data) {
            window.location.href = data.link;
        },

        /** Events */
        handleCommandViewPanelClosed() {
            this.commandViewContent = null;
        },
    },
    created() {
        // default handlers
        this.addCommandActionHandlers({
            'reload': this.handleReloadCommand,
            'info': this.handleInfoCommand,
            'link': this.handleLinkCommand,
            'view': this.handleViewCommand,
            'step': this.handleStepCommand,
        });
    },
    mounted() {
        if(this.$refs.commandForm) {
            this.$refs.commandForm.$on('submit', this.postCommandForm);
            this.$refs.commandForm.$once('close', () => {
                this.$refs.commandForm.$off('submit', this.postCommandForm);
                this.currentCommand = null;
            });
        } else {
            console.error('withCommands: CommandForm not found');
        }
    }
}
