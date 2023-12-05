import localizeField from './field';

export default {
    mixins: [localizeField],

    methods: {
        localizeLabel(label) {
            return this.isLocalized ? label[this.locale] : label;
        },
        localizedOptionLabel(option) {
            return this.localizeLabel(option.label);
        }
    }
}