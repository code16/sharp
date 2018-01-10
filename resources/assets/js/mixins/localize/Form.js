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
            if(!this.localized)
                return;
            console.log(JSON.parse(JSON.stringify(this.data)));
            Object.entries(this.fields).forEach(([key, field]) => {
                //console.log(`field '${field.name}', localized? ${field.localized}`,field.localized ? `empty? ${isLocaleObjectEmpty(this.data[key])}` : '');
                if(field.localized && isLocaleObjectEmpty(this.data[key])) {
                    console.log('is empty' , field, this.data[key]);
                    this.data[key] = null;
                }
            });
            console.log(JSON.parse(JSON.stringify(this.data)));
            // this.redirectToList = ()=>{};
            // this.post = ()=>Promise.resolve();
        }
    }
}