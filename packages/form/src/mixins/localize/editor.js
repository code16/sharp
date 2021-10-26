import localizeField from "./field";
import { localeObjectOrEmpty } from "../../util/locale";

/**
 * Can be used in Editor components
 */
export const LocalizedEditor =  {
    mixins: [localizeField],

    computed: {
        localizedText() {
            if(this.isLocalized) {
                return this.value?.text?.[this.locale] ?? null;
            }

            return this.value?.text ?? null;
        }
    },

    methods: {
        localizedValue(text) {
            return {
                ...this.value,
                text: this.isLocalized
                    ? localeObjectOrEmpty({
                        localeObject: this.value?.text,
                        locale: this.locale,
                        value: text
                    })
                    : text
            };
        }
    }
}
