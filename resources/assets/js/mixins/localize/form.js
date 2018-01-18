import { isLocalizableValueField, localeObjectOrEmpty } from "./utils";

export default {
    methods: {
        fieldLocalizedValue(key, value) {
            let field = this.fields[key];
            if(field.localized && isLocalizableValueField(field)) {
                return localeObjectOrEmpty({ localeObject:this.data[key], locale: this.locale, value });
            }
            return value;
        }
    }
}