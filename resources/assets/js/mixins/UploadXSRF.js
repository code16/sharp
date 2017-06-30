import axios from 'axios';

export default {
    methods: {
        patchXsrf(options) {
            if(!options.headers) options.headers = {};
            options.headers[axios.defaults.xsrfHeaderName] = this.xsrfToken;
            return options;
        }
    }
}