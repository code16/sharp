import { parseBlobJSONContent, getFileName } from "../../util/request";
import { lang, withLoadingOverlay } from "../../index";
import { showConfirm, showAlert } from "../../util/dialogs";

export default {
    data() {
        return {
            currentCommand: null,
            commandViewContent: null,
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
            if(response.data.type === 'application/json') {
                const data = await parseBlobJSONContent(response.data);
                this.handleCommandActionRequested(data.action, data);
            } else {
                this.downloadCommandFile(response);
            }
        },
        async postCommandForm({ postFn }) {
            const response = await this.$refs.commandForm.submit({ postFn });
            await this.handleCommandResponse(response);
            this.currentCommand = null;
        },
        async showCommandForm(command, { postForm, getForm }) {
            const form = command.has_form
                ? await withLoadingOverlay(getForm())
                : {};
            const post = () => this.postCommandForm({ postFn:postForm });


            this.currentCommand = {
                ...command,
                form: this.transformCommandForm(form),
            };

            this.$refs.commandForm.$on('submit', post);
            this.$refs.commandForm.$once('close', () => {
                this.$refs.commandForm.$off('submit', post);
                this.currentCommand = null;
            });
        },
        async sendCommand(command, { postCommand, getForm, postForm }) {
            if(command.has_form) {
                return this.showCommandForm(command, { postForm, getForm });
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
        });
    },
    mounted() {
        if(!this.$refs.commandForm) {
            console.error('withCommands: CommandForm not found');
        }
    }
}
