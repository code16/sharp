const noop = ()=>{};

const LocalizableFields = [
    'text', 'markdown', 'textarea', 'wysiwyg', 'select', 'autocomplete', 'tags'
];
const LocalizableOptionsFields = [
    'select', 'autocomplete', 'tags'
];


function localeObject({ locales, resolve }) {
    return locales.reduce((res, locale)=>({
        ...res, [locale]: resolve(locale)
    }), {});
}

function createLocalizedValue({ locales, type, value }) {
    let textValue, create = noop;
    if(['text', 'textarea'].includes(type)) {
        textValue = value;
        create = val => val;
    }
    else if(['markdown', 'wysiwyg'].includes(type)) {
        textValue = value.text;
        create = val => ({ ...value, text:val, });
    }
    else return value;

    return create(localeObject({ locales, resolve:l=>`${textValue} ${l.toUpperCase()}` }));
}

function createLocalizedOptions({ locales, options=[] }) {
    return options.map(option => ({
        ...option,
        label: localeObject({ locales, resolve:l=>`${option.label} ${l.toUpperCase()}` })
    }));
}

function createLocalizedField({ locales, field }) {
    if(LocalizableFields.includes(field.type)) {
        field.localized = true;
        if(LocalizableOptionsFields.includes(field.type)) {
            let options = field.type === 'autocomplete'? 'localValues': 'options';
            field[options] = createLocalizedOptions({ locales, options: field[options] });
        }
    }
    return field;
}
export default {
    watch: {
        ready() {
            this.ready && this.localize(['fr', 'en']);
        }
    },
    methods: {
        localize(locales) {
            this.$set(this.config,'locales',locales);
            this.actionsBus.$emit('localeChanged', locales[0]);

            this.fields = Object.entries(this.fields).reduce((res, [fieldKey, field]) => ({
                ...res, [fieldKey]: createLocalizedField({ locales, field })
            }), {});

            this.data = Object.entries(this.data).reduce((res, [fieldKey, value]) => ({
                ...res, [fieldKey]: createLocalizedValue({
                    locales, type: this.fields[fieldKey].type, value
                })
            }), {});
        }
    },
}