export const lang = key => {
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