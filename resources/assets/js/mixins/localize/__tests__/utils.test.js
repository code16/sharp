import {
    isLocaleObject,
    localeObjectOrEmpty,
    isLocalizableValueField,
    isLocaleObjectEmpty,
    localeObject
} from "../utils";

describe('localize-utils', ()=>{
    test('isLocaleObject', ()=>{
        expect(isLocaleObject('aaa', ['fr', 'en'])).toBe(false);
        expect(isLocaleObject(1, ['fr', 'en'])).toBe(false);
        expect(isLocaleObject(['aaa', 'bbb'], ['fr', 'en'])).toBe(false);
        expect(isLocaleObject({}, ['fr', 'en'])).toBe(false);
        expect(isLocaleObject({ fr: 'aaa' }, ['fr', 'en'])).toBe(false);
        expect(isLocaleObject({ fr: 'aaa', en: null }, ['fr', 'en'])).toBe(true);
    });
    test('isLocaleObjectEmpty', ()=>{
        expect(isLocaleObjectEmpty({ fr:'aaa', en:'bbb' })).toBe(false);
        expect(isLocaleObjectEmpty({ fr:'aaa', en:null })).toBe(false);
        expect(isLocaleObjectEmpty({ fr:'', en:null })).toBe(true);
        expect(isLocaleObjectEmpty({ fr:null, en:null })).toBe(true);
    });
    test('isLocalizableValueField', ()=>{
        expect(isLocalizableValueField({ type:'text' })).toBe(true);
        expect(isLocalizableValueField({ type:'textarea' })).toBe(true);
        expect(isLocalizableValueField({ type:'markdown' })).toBe(false);
        expect(isLocalizableValueField({ type:'wysiwyg' })).toBe(false);
    });
    test('localeObject', ()=>{
        expect(localeObject({ locales:['fr', 'en'] })).toEqual({ fr:null, en:null });
        expect(localeObject({ locales:['fr', 'en'], resolve:l=>l })).toEqual({ fr:'fr', en:'en' });
    });
    test('localeObjectOrEmpty', ()=>{
        expect(localeObjectOrEmpty({
            localeObject: { fr:'FR text', en:'EN text' },
            locale:'fr',
            value:'new text'
        })).toEqual({ fr:'new text', en:'EN text' });

        expect(localeObjectOrEmpty({
            localeObject: null,
            locale:'fr',
            value:'new text'
        })).toEqual({ fr:'new text' });

        expect(localeObjectOrEmpty({
            localeObject: { en:null },
            locale:'fr',
            value:null
        })).toEqual(null);
    });
});