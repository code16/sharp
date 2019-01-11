import localizeField from './field';
import { isLocaleObject } from "./utils";

export default {
    mixins: [localizeField],
    computed: {
        localizedSearchKeys() {
            return this.localized
                ? this.searchKeys.map(key => {
                    let res = key;
                    if(this.localizedDataKeys.includes(key))
                        res+=`.${this.locale}`;
                    return res;
                })
                : this.searchKeys;
        },
        localizedDataKeys() {
            return (Array.isArray(this.localValues) && this.localValues.length
                ? Object.keys(this.localValues[0]).filter(key => this.isLocaleObject(this.localValues[0][key]))
                : []);
        }
    },
    methods: {
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