import { handleNotifications } from "@/utils/notifications";

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
                return config;
            }, error => Promise.reject(error));

            this.axiosInstance.interceptors.response.use(response => {
                this.hideLoading();
                return response;
            }, error => {
                this.hideLoading();
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
            data: null,
            layout: null,
        }
    },
    methods: {
        get() {
            return this.axiosInstance.get(this.apiPath, {
                    params: this.apiParams
                })
                .then(response => {
                    this.mount(response.data);
                    handleNotifications(response.data.notifications);
                    return Promise.resolve(response);
                });
        },
        post(endpoint = this.apiPath, data = this.data, config) {
            return this.axiosInstance.post(endpoint, data, config);
        },
    }
}
