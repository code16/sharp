import { isLocalizableValueField, localeObject, isLocaleObjectEmpty } from "./utils";

export default {
    methods: {
        isLocalizableValueField,
        normalizeLocalizedValue() {
            if(!this.localized) {
                console.log('form not localized');
                return;
            }
            Object.entries(this.fields).forEach(([key, field]) =>{
                if(field.localized && this.data[key] === null && isLocalizableValueField(field)) {
                    this.$set(this.data, key, localeObject({ locales:this.locales, resolve:()=>null }));
                }
            });
        },
        normalizeLocalizedValueBeforeSubmit() {
            debugger;
            if(!this.localized)
                return;
            Object.entries(this.fields).forEach((key, field) => {
                if(field.localized && isLocaleObjectEmpty(this.data[key])) {
                    console.log('is empty' , field, this.data[key]);
                    this.data[key] = null;
                }
            });
        }
    }
}