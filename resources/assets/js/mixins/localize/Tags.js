import localizeSelect from './Select';
import { localeObject } from "./utils";

import { lang } from "../Localization";

export default {
    extends: localizeSelect,
    methods: {
        localizeLabel(label) { // display
            return this.isLocalized ? label[this.locale] || lang('form.tags.unknown_label') : label;
        },
        localizedTagLabel(text) { // data
            return this.isLocalized ? localeObject({
                locales:this.locales,
                resolve:l => l===this.locale ? text : null
            }) : text;
        },
        localizedCustomLabel(option) {
            return this.localizeLabel(option.label)
        }
    }
}