import Localization ,* as localizationFn from '../../mixins/Localization';

const mockLangImplementation = localeKey => `{{ ${localeKey} }}`;

export default {
    install() {
        window.i18n = {};
    },

    mockLangFunction() {
        localizationFn.lang = jest.fn(mockLangImplementation);
        Localization.methods.l = jest.fn(mockLangImplementation);
    }
}