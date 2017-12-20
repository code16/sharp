import localizeMultiselect from './Multiselect';
import { localeObject } from "./utils";

import { lang } from "../Localization";

export default {
    extends: localizeMultiselect,
    methods: {
        localizeLabel(label) { // display
            return this.localized ? label[this.locale] || lang('form.tags.unknown_label') : label;
        },
        localizedTagLabel(text) { // data
            return this.localized ? localeObject({
                locales:this.locales,
                resolve:l => l===this.locale ? text : null
            }) : text;
        },
    }
}