import { parseBlobJSONContent } from "../util";
import { lang } from '../mixins/Localization';
import { BASE_URL } from "../consts";

export const withAxiosInterceptors = {
    inject: ['mainLoading', 'axiosInstance', 'actionsBus'],
    methods: {
        installInterceptors() {
            this.axiosInstance.interceptors.request.use(config => {
                this.mainLoading.$emit('show');
                //debugger
                return config;
            }, error => Promise.reject(error));

            this.axiosInstance.interceptors.response.use(response => {
                this.mainLoading.$emit('hide');
                return response;
            }, async error => {
                let { response, config: { method } } = error;
                this.mainLoading.$emit('hide');

                if(response.data instanceof Blob && response.data.type === 'application/json') {
                    response.data = await parseBlobJSONContent(response.data);
                }

                let { data, status } = response;

                let modalOptions = {
                    title: lang(`modals.${status}.title`) || lang(`modals.error.title`),
                    text: data.message || lang(`modals.${status}.message`) || lang(`modals.error.message`),
                    isError: true
                };

                if(status === 419) {
                    modalOptions.okCallback = () => location.reload();
                }

                switch(status) {
                    /// Unauthorized
                    case 401: this.actionsBus.$emit('showMainModal', {
                        ...modalOptions,
                        okCallback() {
                            location.href = `${BASE_URL}/login`;
                        },
                    });
                        break;

                    case 403:
                    case 404:
                    case 417:
                    case 419:
                    case 500:
                        if(status !== 404 || method !== 'get')
                            this.actionsBus.$emit('showMainModal', {
                                ...modalOptions,
                                okCloseOnly:true,
                            });
                        break;
                }
                return Promise.reject(error);
            });
        },
    },
    created() {
        if(!this.synchronous) {
            this.installInterceptors();
            this.mainLoading.$emit('show');
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
                .then(response=>{
                    this.mount(response.data);
                    this.handleNotifications(response.data);
                    return Promise.resolve(response);
                })
                .catch(error=>{
                    return Promise.reject(error);
                });
        },
        post(endpoint = this.apiPath, data = this.data, config) {
            return this.axiosInstance.post(endpoint, data, config).then(response=>{
                    return Promise.resolve(response);
                })
                .catch(error=>{
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