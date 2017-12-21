import { __createLocalizedField, __createLocalizedValue, __createLocalizedAutocompleteSuggestions } from "./test-utils";

const testLocales = ['fr', 'en'];

export const testLocalizedForm = {
    watch: {
        ready() {
            this.ready && this.__localize(testLocales);
        }
    },
    methods: {
        __localize(locales) {
            this.locales = locales;
            this.actionsBus.$emit('localeChanged', locales[0]);

            this.fields = Object.entries(this.fields).reduce((res, [fieldKey, field]) => ({
                ...res, [fieldKey]: __createLocalizedField({ locales, field })
            }), {});

            this.data = Object.entries(this.data).reduce((res, [fieldKey, value]) => ({
                ...res, [fieldKey]: __createLocalizedValue({
                    locales, type: this.fields[fieldKey].type, value
                })
            }), {});
        },
    }
};

const lockWatcher = {
    methods: {
        async _lockWatcher(name, callback) {
            if(this._lockedWatchers[name])return;
            this._lockedWatchers[name] = true;
            callback();
            await this.$nextTick();
            this._lockedWatchers[name] = false;
        },
    },
    created() {
        this._lockedWatchers = {};
    }
};

export const testLocalizedAutocomplete = {
    mixins: [lockWatcher],
    watch: {
        suggestions() {
            this._lockWatcher('suggestions', () => {
                this.suggestions = __createLocalizedAutocompleteSuggestions({
                    suggestions:this.suggestions,
                    locales:testLocales
                });
            });
        }
    }
};

export const testLocalizedList = {
    mixins: [lockWatcher],
    watch: {
        list() {
            this._lockWatcher('list', () => {

            });
        }
    }
};