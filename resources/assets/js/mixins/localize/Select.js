import localizeField from './field';
import { isLocaleObject } from "./utils";

export default {
    mixins: [localizeField],

    methods: {
        localizeLabel(label) {
            return this.localized ? label[this.locale] : label;
        },
        localizedCustomLabel(option) {
            return this.localizeLabel(option.label);
        }
    }
}