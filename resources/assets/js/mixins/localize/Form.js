import { isLocalizableValueField, localeObject } from "./utils";

export default {
    methods: {
        isLocalizableValueField,
        normalizeLocalizedValue() {
            Object.entries(this.fields).forEach(([key, field]) =>{
                if(field.localized && this.data[key] === null && isLocalizableValueField(field)) {
                    this.$set(this.data, key, localeObject({ locales:this.locales, resolve:()=>null }));
                }
            });
        }
    }
}