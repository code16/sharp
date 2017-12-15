export let lang = (key) => {
    return window.i18n[key];
};

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