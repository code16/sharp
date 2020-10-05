import Vue from 'vue';

export function mockInjections({ locales, localized }) {
    return {
        $form:new Vue({ data:()=>({ locales, localized }) })
    }
}