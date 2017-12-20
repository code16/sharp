import Vue from 'vue';
import { isLocaleObject } from "./utils";

export default {
    inject: {
        $form: { default:()=>new Vue() }
    },
    props: {
        locale: String,
        localized: Boolean
    },
    computed:{
        locales() {
            return (this.$form.config||{}).locales;
        }
    },
    methods: {
        localizeLabel(label) {
            return this.localized ? label[this.locale] : label;
        },
        localizedCustomLabel(option) {
            return this.localizeLabel(option.label);
        },
        isLocaleObject(obj) {
            return this.locales && isLocaleObject(obj, this.locales);
        },
        localizedTemplateData(templateData) {
            return this.localized ? Object.entries(templateData).reduce((res, [key, value]) => {
                res[key] = this.isLocaleObject(value) ? value[this.locale] : value;
                return res;
            }, {}) : templateData;
        }
    }
}