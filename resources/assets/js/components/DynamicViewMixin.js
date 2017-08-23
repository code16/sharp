import * as qs from '../helpers/querystring';

import { lang } from '../mixins/Localization';

export default {
    inject:['mainLoading', 'axiosInstance'],
    
    data() {
        return {
            data:null,
            layout:null,

            ready:false,
        }
    },
    methods: {
        get() {
            if(this.test) return;

            return this.axiosInstance.get(this.apiPath, {
                    params : this.apiParams,
                    paramsSerializer : p => qs.serialize(p, {urlSeparator:false})
                })
                .then(response=>{
                    this.mount(response.data);
                    this.ready = true;
                    return Promise.resolve(response);
                })
                .catch(error=>{
                    return Promise.reject(error);
                });
        },
        post(endpoint = this.apiPath, data = this.data) {
            return this.axiosInstance.post(endpoint, data).then(response=>{
                    return Promise.resolve(response);
                })
                .catch(error=>{
                    return Promise.reject(error);
                });
        }
    },
    created() {
        this.axiosInstance.interceptors.request.use(config => {
            this.mainLoading.$emit('show');
            //debugger
            return config;
        }, error => Promise.reject(error));

        this.axiosInstance.interceptors.response.use(response => {
            this.mainLoading.$emit('hide');
            return response;
        }, error => {
            this.mainLoading.$emit('hide');
            let modalOptions = {
                title: lang(`modals.${error.response.status}`),
                text: error.response.data.message,
                isError: true
            };

            switch(error.response.status) {
                /// Unauthorized
                case 401: this.actionsBus.$emit('showMainModal', {
                    ...modalOptions,
                    okCallback() {
                        location.href = '/sharp/login';
                    },
                });
                    break;
                /// Forbidden
                case 403:
                case 417:
                    this.actionsBus.$emit('showMainModal', {
                    ...modalOptions,
                    okCloseOnly:true,
                });
                    break;
            }
            return Promise.reject(error);
        });

        if(!this.synchronous)
            this.mainLoading.$emit('show');
    }
}