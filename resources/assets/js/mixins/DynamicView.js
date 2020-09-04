import { parseBlobJSONContent } from "../util/request";
import { lang } from '../util/i18n';
import { showAlert } from "../util/dialogs";

export const withAxiosInterceptors = {
    inject: ['axiosInstance'],
    methods: {
        installInterceptors() {
            this.axiosInstance.interceptors.request.use(config => {
                this.$store.dispatch('setLoading', true);
                //debugger
                return config;
            }, error => Promise.reject(error));

            this.axiosInstance.interceptors.response.use(response => {
                this.$store.dispatch('setLoading', false);
                return response;
            }, async error => {
                let { response, config: { method } } = error;
                this.$store.dispatch('setLoading', false);

                if(response.data instanceof Blob && response.data.type === 'application/json') {
                    response.data = await parseBlobJSONContent(response.data);
                }

                let { data, status } = response;

                const text = data.message || lang(`modals.${status}.message`) || lang(`modals.error.message`);
                const title = lang(`modals.${status}.title`) || lang(`modals.error.title`);

                if(status === 404 && method === 'get' || status === 422) {
                    return Promise.reject(error);
                }

                if(status === 401 || status === 419) {
                    showAlert(text, {
                        title,
                        isError: true,
                        okCallback() {
                            location.reload();
                        },
                    });
                }
                else {
                    showAlert(text, { title, isError:true });
                }

                return Promise.reject(error);
            });
        },
    },
    created() {
        if(!this.synchronous) {
            this.installInterceptors();
            this.$store.dispatch('setLoading', true);
        }
    }
};

export default {
    mixins: [withAxiosInterceptors],
    inject: ['axiosInstance'],

    data() {
        return {
            data:null,
            layout:null,
        }
    },
    methods: {
        get() {
            return this.axiosInstance.get(this.apiPath, {
                    params : this.apiParams
                })
                .then(response => {
                    this.mount(response.data);
                    this.handleNotifications(response.data);
                    return Promise.resolve(response);
                })
                .catch(error => {
                    return Promise.reject(error);
                });
        },
        post(endpoint = this.apiPath, data = this.data, config) {
            return this.axiosInstance.post(endpoint, data, config).then(response => {
                    return Promise.resolve(response);
                })
                .catch(error => {
                    return Promise.reject(error);
                });
        },
        showNotification({ level, title, message, autoHide }) {
            this.$notify({
                title,
                type: level,
                text: message,
                duration: autoHide ? 4000 : -1
            });
        },
        handleNotifications(data={}) {
            if(Array.isArray(data.notifications)) {
                setTimeout(() => data.notifications.forEach(this.showNotification), 500);
            }
        },
    }
}