import * as Localization from '../../mixins/Localization';

export default {
    install() {
        Localization.lang = jest.fn(localeKey => `{{ ${localeKey} }}`);
        //window.i18n = {};
    }
}