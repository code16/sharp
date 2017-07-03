import axios from 'axios';

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
            return axios.get(this.apiPath).then(response=>{
                    this.mount(response.data);
                    this.ready = true;
                this.glasspane.$emit('hide');
                    return Promise.resolve(response);
                })
                .catch(error=>{
                    this.glasspane.$emit('hide');
                    return Promise.reject(error);
                });
        },
        post() {
            this.glasspane.$emit('show');
            return axios.post(this.apiPath, this.data).then(response=>{
                this.glasspane.$emit('hide');
                    return Promise.resolve(response);
                })
                .catch(error=>{
                    this.glasspane.$emit('hide');
                    return Promise.reject(error);
                });
        }
    },
    created() {
        axios.interceptors.response.use(response => response, error => {
            switch(error.response.status) {
                case 401: this.actionsBus.$emit('showMainModal', {
                    title: lang('modals.401'),
                    text: "Vous n'êtes plus connecté",
                    okCallback() {
                        location.href = '/sharp/login';
                    },
                    isError: true
                });
                    break;
                case 403: this.actionsBus.$emit('showMainModal', {
                    title: lang('modals.401'),
                    text: "Vous nêtes pas autorisé à effectuer cette action",
                    okCloseOnly:true,
                    isError: true
                });
                    break;
            }
            return Promise.reject(error);
        });

        this.glasspane.$emit('show');
    }
}