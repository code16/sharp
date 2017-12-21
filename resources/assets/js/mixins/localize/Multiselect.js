import localizeField from './field';
import { isLocaleObject } from "./utils";

export default {
    mixins: [localizeField],
    props: {
        locale: String,
        localized: Boolean
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