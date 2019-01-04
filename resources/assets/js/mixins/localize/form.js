import { isLocalizableValueField, localeObjectOrEmpty } from "./utils";

export default function (fieldsProp) {
    return {
        _localizedForm: fieldsProp,
        methods: {
            fieldLocalizedValue(key, value, data=this.data, fieldLocale=this.fieldLocale) {
                let field = this[fieldsProp][key];
                if(this.localized && field.localized && isLocalizableValueField(field)) {
                    return localeObjectOrEmpty({
                        localeObject: data[key],
                        locale: fieldLocale[key],
                        value
                    });
                }
                return value;
            },
            defaultFieldLocaleMap({ fields, locales }) {
                return Object.values(fields)
                    .filter(field => field.localized)
                    .reduce((res, field) => ({ ...res, [field.key]:locales && locales[0] }),{})
            }
        },
    }
}