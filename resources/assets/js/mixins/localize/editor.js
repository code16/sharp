import localizeField from "./field";
import { localeObjectOrEmpty } from "./utils";

/**
 * Can be used in Markdown, TrixEditor component
 */
export default function ({ textProp }) {
    return {
        _localizedEditor: { textProp },
        mixins: [localizeField],

        computed: {
            localizedText() {
                return (
                    this.isLocalized
                    ? this.value[textProp] !== null ? this.value[textProp][this.locale] : ''
                    : this.value[textProp]
                );
            }
        },

        methods: {
            localizedValue(text) {
                return {
                    ...this.value,
                    [textProp]: this.isLocalized
                        ? localeObjectOrEmpty({ localeObject:this.value[textProp], locale:this.locale, value: text })
                        : text
                };
            }
        }
    }
}