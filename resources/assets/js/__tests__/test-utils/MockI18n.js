import Localization,* as localizationFn from '../../mixins/Localization';

const mockLangImplementation = localeKey => `{{ ${localeKey} }}`;
function mockLangFunction() {
    localizationFn.lang = jest.fn(mockLangImplementation);
    Localization.methods.l = jest.fn(mockLangImplementation);
    localizationFn.LocalizationBase = baseKey => ({
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