import axios from 'axios';

import * as qs from '../helpers/querystring';

import { lang } from '../mixins/Localization';

export default {
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

            return axios.get(this.apiPath, {
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
            return axios.post(endpoint, data).then(response=>{
                    return Promise.resolve(response);
                })
                .catch(error=>{
                    return Promise.reject(error);
                });
        }
    },
    created() {
        axios.interceptors.request.use(config => {
            //console.log('request interceptor', config);
            //if(config.method==='get')
                this.glasspane.$emit('show');
            return config;
        }, error => Promise.reject(error));

        axios.interceptors.response.use(response => {
            this.glasspane.$emit('hide');
            return response;
        }, error => {
            this.glasspane.$emit('hide');
            let modalOptions = {
                title: lang(`modals.${error.response.status}`),
                text: error.response.data.message,
                isError: true
            };

            switch(error.response.status) {
                case 401: this.actionsBus.$emit('showMainModal', {
                    ...modalOptions,
                    okCallback() {
                        location.href = '/sharp/login';
                    },
                });
                    break;
                case 403: this.actionsBus.$emit('showMainModal', {
                    ...modalOptions,
                    okCloseOnly:true,
                });
                    break;
            }
            return Promise.reject(error);
        });

        if(!this.synchronous)
            this.glasspane.$emit('show');
    }
}