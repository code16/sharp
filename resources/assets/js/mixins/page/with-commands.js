import { parseBlobJSONContent, getFileName } from "../../util";

export default {
    data() {
        return {
            commandCurrentForm: null,
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
                const handler = this.commandHandlers[data.action];

                if(handler) {
                    handler(data);
                }
            } else {
                this.downloadCommandFile(response);
            }
        },
        async postCommandForm({ postFn }) {
            const response = await this.$refs.commandForm.submit({ postFn });
            await this.handleCommandResponse(response);
            this.commandCurrentForm = null;
        },
        async showCommandForm(command, { postForm, getFormData }) {
            const data = command.fetch_initial_data ? await getFormData() : {};
            const post = () => this.postCommandForm({ postFn:postForm });

            this.commandCurrentForm = this.transformCommandForm({ ...command.form, data });

            this.$refs.commandForm.$on('submit', post);
            this.$refs.commandForm.$once('close', () => {
                this.$refs.commandForm.$off('submit', post);
                this.commandCurrentForm = null;
            });
        },
        async sendCommand(command, { postCommand, getFormData, postForm }) {
            const { form, confirmation } = command;
            if(form) {
                return this.showCommandForm(command, { postForm, getFormData });
            }
            if(confirmation) {
                await new Promise(resolve => {
                    this.actionsBus.$emit('showMainModal', {
                        title: this.l('modals.command.confirm.title'),
                        text: confirmation,
                        okCallback: resolve,
                    });
                });
            }
            try {
                let response = await postCommand();
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

        /** Command actions handlers */
        handleReloadCommand() {
            this.init();
        },
        handleInfoCommand(data) {
            this.actionsBus.$emit('showMainModal', {
                title: this.l('modals.command.info.title'),
                text: data.message,
                okCloseOnly: true
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