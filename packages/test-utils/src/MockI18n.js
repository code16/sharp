import * as i18n from 'sharp/util/i18n';
import * as mixins from 'sharp/mixins';
import * as localizationModule from 'sharp/mixins/Localization';


const mockLangImplementation = localeKey => `{{ ${localeKey} }}`;
function mockLangFunction() {
    i18n.lang = jest.fn(mockLangImplementation);
    mixins.Localization.methods.l = jest.fn(mockLangImplementation);
    localizationModule.LocalizationBase = baseKey => ({
        methods: {
            lSub: jest.fn(key => mockLangImplementation(`${baseKey}.${key}`))
        }
    });
}

export default {
    install(Vue, { mockFn=true }={}) {
        window.i18n = {};
        mockFn && mockLangFunction();
    },
    mockLangFunction
}
