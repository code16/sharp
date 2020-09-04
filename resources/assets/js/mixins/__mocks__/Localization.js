const mockLangImplementation = localeKey => `{{ ${localeKey} }}`;

export default {
    methods: {
        l: mockLangImplementation
    }
}

export function LocalizationBase(baseKey) {
    return {
        methods: {
            lSub(key) {
                return mockLangImplementation(`${baseKey}.${key}`)
            }
        }
    }
}
