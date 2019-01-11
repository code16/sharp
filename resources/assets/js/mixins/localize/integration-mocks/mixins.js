import {
    __createLocalizedField,
    __createLocalizedValue,
    __createLocalizedAutocompleteSuggestions
} from "./utils";

const testLocales = ['fr', 'en'];

const testLocalizeMixin = {
    computed: {
        activateTest: () => location.search.includes('mock')
    },
    methods: {
        _lockWatcher(name, callback) {
            return async (val, oldVal) => {
                if(this._lockedWatchers[name])return;
                this._lockedWatchers[name] = true;
                callback(val, oldVal);
                await this.$nextTick();
                this._lockedWatchers[name] = false;
            }
        },
        _addTestWatcher(prop, callback) {
            this.activateTest && this.$watch(prop, callback);
        },
        _addLockedTestWatcher(prop, callback) {
            this.activateTest && this.$watch(prop, this._lockWatcher(prop, callback));
        }
    },
    created() {
        this._lockedWatchers = {};
    }
};

export const testLocalizedForm = {
    mixins:[testLocalizeMixin],

    methods: {
        __localize() {
            if(!this.ready) return;

            this.locales = testLocales;
            this.actionsBus.$emit('localeChanged', this.locales[0]);

            this.data = Object.entries(this.data).reduce((res, [fieldKey, value]) => ({
                ...res, [fieldKey]: __createLocalizedValue({
                    locales: this.locales, type: this.fields[fieldKey].type, value
                })
            }), {});
        },
    },
    created() {
        this._addTestWatcher('ready', this.__localize);
        this._addLockedTestWatcher('fields',()=>{
            this.fields = Object.entries(this.fields).reduce((res, [fieldKey, field]) => ({
                ...res, [fieldKey]: __createLocalizedField({ locales: this.locales, field })
            }), {});
        });
    }
};

export const testLocalizedAutocomplete = {
    mixins: [testLocalizeMixin],
    methods: {
        __localizeSuggestions() {
            this.suggestions = __createLocalizedAutocompleteSuggestions({
                suggestions:this.suggestions,
                locales:testLocales
            });
        }
    },
    created() {
        this._addLockedTestWatcher('suggestions', this.__localizeSuggestions);
    }
};

export const testLocalizedList = {
    mixins: [testLocalizeMixin],
    methods: {
        __localize() {

        }
    },
    created() {
        this._addLockedTestWatcher('list', this.__localize);
    },
};