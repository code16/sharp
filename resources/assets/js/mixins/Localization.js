import { lang } from '../index';

export default {
    computed: {
        language() {
            return document.documentElement.lang;
        }
    },
    methods: {
        l: lang
    }
}
export let LocalizationBase = baseKey => {
    return {
        methods: {
            lSub(key) {
                return lang(`${baseKey}.${key}`)
            }
        }
    }
};