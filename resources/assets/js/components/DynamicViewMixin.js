import axios from 'axios';

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
            if(this.test)return;
            return new Promise((resolve, reject) => {
                axios.get(this.apiPath).then(({data})=>{
                    this.mount(data);
                    this.ready = true;
                    resolve();
                });
            });
        },
        post() {
            return axios.post(this.apiPath, this.data);
        }
    }
}