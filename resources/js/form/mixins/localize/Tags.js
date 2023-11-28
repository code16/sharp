import { __ } from "@/utils/i18n";

import localizeSelect from './Select';
import { localeObject } from "../../util/locale";

export default {
    extends: localizeSelect,
    methods: {
        localizeLabel(label) { // display
            return this.isLocalized ? label[this.locale] || __('sharp::form.tags.unknown_label') : label;
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
