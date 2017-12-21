import localizeField from "./field";

/**
 * Can be used in Markdown, TrixEditor component
 */
export default function ({ textProp }) {
    return {
        mixins: [localizeField],
        props: {
            type: String,
            locale: String,
            localized: Boolean
        },

        methods: {
            localizedValue(text) {
                let res = { ...this.value };
                if(this.localized) {
                    res[textProp][this.locale] = text;
                }
                else {
                    res[textProp] = text;
                }
                return res;
            }
        },
        created() {
            if(this.localized && this.value[textProp] === null) {
                this.value.text = this.emptyLocaleObject('');
            }
        }
    }
}