import { isLocalizableValueField, localeObjectOrEmpty } from "./utils";

export default function (fieldsProp) {
    return {
        methods: {
            fieldLocalizedValue(key, value, data=this.data) {
                let field = this[fieldsProp][key];
                if(this.localized && field.localized && isLocalizableValueField(field)) {
                    return localeObjectOrEmpty({ localeObject:data[key], locale: this.locale, value });
                }
                return value;
            }
        }
    }
}