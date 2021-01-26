import { isLocalizableValueField, localeObjectOrEmpty } from "../../util/locale";

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
            defaultFieldLocaleMap({ fields, locales }, locale) {
                return Object.values(fields)
                    .filter(field => {
                        if(field.type === 'list') {
                            return Object.values(field.itemFields ?? {}).some(field => field.localized);
                        }
                        return field.localized;
                    })
                    .reduce((res, field) => ({
                        ...res,
                        [field.key]: locale || locales && locales[0],
                    }),{})
            }
        },
    }
}
