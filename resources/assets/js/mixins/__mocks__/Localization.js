const mockLangImplementation = localeKey => `{{ ${localeKey} }}`;

export default {
    methods: {
        l: mockLangImplementation
    }
}

export const lang = mockLangImplementation;
