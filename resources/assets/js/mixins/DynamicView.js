import { parseBlobJSONContent } from "../util/request";
import { lang } from '../util/i18n';
import { showAlert } from "../util/dialogs";
import { handleNotifications } from "../util/notifications";

export const withAxiosInterceptors = {
    inject: ['axiosInstance'],
    methods: {
        showLoading() {
            this.$store.dispatch('setLoading', true);
        },
        hideLoading() {
            this.$store.dispatch('setLoading', false);
        },
        installInterceptors() {
            this.axiosInstance.interceptors.request.use(config => {
                this.showLoading();
                //debugger
                return config;
            }, error => Promise.reject(error));

            this.axiosInstance.interceptors.response.use(response => {
                this.hideLoading();
                return response;
            }, async error => {
                let { response, config: { method } } = error;
                this.hideLoading();

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
            this.showLoading();
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
                    handleNotifications(response.data.notifications);
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
    }
}
