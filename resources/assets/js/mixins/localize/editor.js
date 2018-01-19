import localizeField from "./field";
import { localeObjectOrEmpty } from "./utils";

/**
 * Can be used in Markdown, TrixEditor component
 */
export default function ({ textProp }) {
    return {
        mixins: [localizeField],

        computed: {
            localizedText() {
                return this.value[textProp] !== null ? this.value[textProp][this.locale] : '';
            }
        },

        methods: {
            localizedValue(text) {
                return {
                    ...this.value,
                    [textProp]: this.localized
                        ? localeObjectOrEmpty({ localeObject:this.value[textProp], locale:this.locale, value: text })
                        : text
                };
            }
        }
    }
}